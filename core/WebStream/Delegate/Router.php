<?php
namespace WebStream\Delegate;

use WebStream\Module\Security;
use WebStream\Module\Logger;
use WebStream\Exception\Extend\RouterException;

/**
 * ルーティングクラス
 * @author Ryuichi TANAKA.
 * @since 2011/08/19
 * @version 0.4
 */
class Router
{
    /** ルーティングルール */
    private static $rules;
    /** PATH_INFO */
    private $pathInfo;
    /** RequestURI */
    private $requestUri;
    /** ルーティング解決後の各パラメータ */
    private $route;

    /**
     * コンストラクタ
     * @param Object リクエストオブジェクト
     */
    public function __construct($request)
    {
        $this->pathInfo = $request->getPathInfo();
        $this->requestUri = $request->getRequestUri();
        Logger::info("Request URI: " . $this->requestUri);
    }

    /**
     * デストラクタ
     */
    public function __destruct()
    {
        Logger::debug("Router is clear.");
    }

    /**
     * ルーティングルールをセットする
     * @param Hash ルーティングルール定義
     */
    public static function setRule($rules)
    {
        self::$rules = $rules;
    }

    /**
     * ルーティングルールを検証する
     */
    private function validate()
    {
        // パス定義部分('/xxx')は禁止の定義がされた時点でエラーとする
        // CA部分('controller#action')はパスにアクセスされたときにチェックする
        foreach (self::$rules as $path => $ca) {
            // 静的ファイルへのパスがルーティングルールに定義された場合
            // パス定義された時点で弾く
            if (preg_match('/\/(img|js|css|file)(?:$|\/)/', $path)) {
                throw new RouterException("Include the prohibit routing path: " . $path);
            }
            // 許可したルーティングパス定義に合っていなければ弾く
            if (!preg_match('/^\/{1}(?:$|:?[a-zA-Z]{1}[a-zA-Z0-9-_\/\.:]{0,}$)/', $path)) {
                throw new RouterException("Invalid path defintion: " . $path);
            }
            // ルールとURLがマッチした場合に動的にチェックを掛ける
            // パスがマッチしたときにアクション名をチェックし、その時点で弾く
            if ($this->pathInfo === $path) {
                // ルーティング定義(Controller#Action)が正しい場合
                // _(アンダースコア)は許可するが、２回以上の連続の場合、末尾につく場合は許可しない
                // NG例：my__blog, my_blog_
                if (!preg_match('/^(?:([a-z]{1}(?:_(?=[a-z])|[a-z0-9])+))#(?:([a-z]{1}(?:_(?=[a-z])|[a-z0-9])+))$/', $ca, $matches)) {
                    // ルーティング定義(Controller#Action)が正しくない場合
                    throw new RouterException("Invalid controller#action definition: " . $ca);
                }
            }
        }
    }

    /**
     * ルーティングルールを各パスに分解する
     * @param array ルーティングルール
     */
    public function resolve()
    {
        // ルーティングルールの検証
        $this->validate();
        // 静的ファイルの存在チェック
        $staticFile = STREAM_APP_ROOT . "/app/views/" . STREAM_VIEW_PUBLIC . $this->pathInfo;
        if (is_file($staticFile)) {
            $this->route["staticFile"] = $staticFile;

            return;
        } elseif (pathinfo($staticFile, PATHINFO_EXTENSION) == 'css') {
            // cssファイル指定かつ存在しない場合で、同ディレクトリ内にlessファイルがあればcssにコンパイルする
            $less = new \lessc();
            $dirpath = dirname($staticFile);
            $filenameWitoutExt = pathinfo($staticFile, PATHINFO_FILENAME);
            $lessFilepath = $dirpath . "/" . $filenameWitoutExt . ".less";
            if (@$less->checkedCompile($lessFilepath, $staticFile)) {
                if (is_file($staticFile)) {
                    $this->route["staticFile"] = $staticFile;
                } else {
                    Logger::error("Failed to file create, cause parmission denied: " . $dirpath);
                }

                return;
            }
        }
        // ルーティングルールからController、Actionを取得
        foreach (self::$rules as $path => $ca) {
            $route = [];
            $tokens = explode("/", ltrim($path, "/"));
            $route["params"] = [];
            $key_list = [];

            for ($i = 0; $i < count($tokens); $i++) {
                $token = $tokens[$i];
                // PATH定義にプレースホルダがある場合は正規表現に置き換える
                if (preg_match('/:(.*?)(?:\/|$)/', $token, $matches)) {
                    $key_list[] = $matches[1];
                    $token = preg_replace('/(:.*?)(?:\/|$)/', '(.+)', $token);
                }
                $tokens[$i] = $token;
            }
            // プレースホルダのパラメータをセット
            $expantionPath = $path;
            // PATH_INFOの階層数とルーティング定義の階層数が一致すればルーティングがマッチ
            if (($this->pathInfo !== $path) &&
                count(explode('/', $path)) === count(explode('/', $this->pathInfo))) {
                // プレースホルダと実URLをひもづける
                $path_pattern = "/^\/" . implode("\/", $tokens) . "$/";
                if (preg_match($path_pattern, $this->pathInfo, $matches)) {
                    for ($j = 1; $j < count($matches); $j++) {
                        $key = $key_list[$j - 1];
                        $route["params"][$key] = Security::safetyIn($matches[$j]);
                        // プレースホルダを一時展開する
                        $expantionPath = preg_replace('/:[a-zA-Z_]{1}[a-zA-Z0-9_]{0,}/', $matches[$j], $expantionPath, 1);
                    }
                }
            }

            // プレースホルダを展開済みのパス定義が完全一致したときはController、Actionを展開する
            if ($this->pathInfo === $expantionPath &&
                preg_match('/^(?:([a-z]{1}(?:_(?=[a-z])|[a-z0-9])+))#(?:([a-z]{1}(?:_(?=[a-z])|[a-z0-9])+))$/', $ca, $matches)) {
                $route["controller"] = $matches[1];
                $route["action"] = $matches[2];
            }

            // ルーティングルールがマッチした場合は抜ける
            if (isset($route["controller"]) && isset($route["action"])) {
                Logger::info("Routed path: " . $route["controller"] . "#" . $route["action"]);
                $this->route = $route;
                break;
            }
        }
    }

    /**
     * ルーティングパラメータを返却する
     * @return String コントローラ名
     */
    public function routingParams()
    {
        return $this->route;
    }

    /**
     * コントローラ名を返却する
     * @return String コントローラ名
     */
    public function controller()
    {
        $controller = null;
        if (isset($this->route['controller'])) {
            // _[a-z]を[A-Z]に置換する
            $controller = preg_replace_callback('/_(?=[a-z])(.+?)/', function ($matches) {
                return ucfirst($matches[1]);
            }, $this->route["controller"]);
            $controller = ucfirst($controller) . "Controller";
        }

        return $controller;
    }

    /**
     * アクション名を返却する
     * @return String アクション名
     */
    public function action()
    {
        $action = null;
        if (isset($this->route['action'])) {
            // _[a-z]を[A-Z]に置換する
            $action = preg_replace_callback('/_(?=[a-z])(.+?)/', function ($matches) {
                return ucfirst($matches[1]);
            }, $this->route["action"]);
        }

        return $action;
    }

    /**
     * パラメータを返却する
     * @return array ラメータ
     */
    public function params()
    {
        return isset($this->route["params"]) ?
            $this->route["params"] : null;
    }

    /**
     * 静的ファイルパスを返却する
     * @return string 静的ファイルパス
     */
    public function staticFile()
    {
        return $this->route["staticFile"];
    }
}
