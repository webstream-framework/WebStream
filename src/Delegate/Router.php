<?php
namespace WebStream\Delegate;

use WebStream\DI\Injector;
use WebStream\Container\Container;
use WebStream\Util\Security;
use WebStream\Util\CommonUtils;
use WebStream\Exception\Extend\RouterException;

/**
 * ルーティングクラス
 * @author Ryuichi TANAKA.
 * @since 2011/08/19
 * @version 0.7
 */
class Router
{
    use Injector, CommonUtils;

    /**
     * @var array<string> ルーティングルール
     */
    private $rules;

    /**
     * @var Container リクエストコンテナ
     */
    private $request;

    /**
     * @var Container ルーティング結果
     */
    private $routingContainer;

    /**
     * コンストラクタ
     * @param Request リクエストオブジェクト
     */
    public function __construct(array $rules, Container $request)
    {
        $this->rules = $rules;
        $this->request = $request;
        $this->routingContainer = new Container(false);
    }

    /**
     * デストラクタ
     */
    public function __destruct()
    {
        $this->logger->debug("Router is clear.");
    }

    /**
     * ルーティングを解決する
     */
    public function resolve()
    {
        // ルーティングルールの検証
        $this->validate();
        // ルーティング結果の格納
        $this->resolveRouting() ?: $this->resolveStaticFilePath();
    }

    /**
     * ルーティング結果を返却する
     * @return Container ルーティング結果
     */
    public function getRoutingResult()
    {
        return $this->routingContainer;
    }

    /**
     * ルーティングルールを検証する
     */
    private function validate()
    {
        // パス定義部分('/xxx')は禁止の定義がされた時点でエラーとする
        // CA部分('controller#action')はパスにアクセスされたときにチェックする
        foreach ($this->rules as $path => $ca) {
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
            if ($this->request->pathInfo === $path) {
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
     * ルーティングを解決する
     */
    private function resolveRouting()
    {
        // ルーティングルールからController、Actionを取得
        foreach ($this->rules as $path => $ca) {
            $route = [];
            $tokens = explode("/", ltrim($path, "/"));
            $placeholderedParams = [];
            $keyList = [];

            for ($i = 0; $i < count($tokens); $i++) {
                $token = $tokens[$i];
                // PATH定義にプレースホルダがある場合は正規表現に置き換える
                if (preg_match('/:(.*?)(?:\/|$)/', $token, $matches)) {
                    $keyList[] = $matches[1];
                    $token = preg_replace('/(:.*?)(?:\/|$)/', '(.+)', $token);
                }
                $tokens[$i] = $token;
            }
            // プレースホルダのパラメータをセット
            $expantionPath = $path;
            // PATH_INFOの階層数とルーティング定義の階層数が一致すればルーティングがマッチ
            if (($this->request->pathInfo !== $path) &&
                count(explode('/', $path)) === count(explode('/', $this->request->pathInfo))) {
                // プレースホルダと実URLをひもづける
                $pathPattern = "/^\/" . implode("\/", $tokens) . "$/";
                if (preg_match($pathPattern, $this->request->pathInfo, $matches)) {
                    for ($j = 1; $j < count($matches); $j++) {
                        $key = $keyList[$j - 1];
                        $placeholderedParams[$key] = Security::safetyIn($matches[$j]);
                        // プレースホルダを一時展開する
                        $expantionPath = preg_replace('/:[a-zA-Z_]{1}[a-zA-Z0-9_]{0,}/', $matches[$j], $expantionPath, 1);
                    }
                }
            }

            // プレースホルダを展開済みのパス定義が完全一致したときはController、Actionを展開する
            if ($this->request->pathInfo === $expantionPath &&
                preg_match('/^(?:([a-z]{1}(?:_(?=[a-z])|[a-z0-9])+))#(?:([a-z]{1}(?:_(?=[a-z])|[a-z0-9])+))$/', $ca, $matches)) {
                $this->setController($matches[1]);
                $this->setAction($matches[2]);
                $this->routingContainer->params = $placeholderedParams;
                $this->logger->info("Routed path: " . $matches[1] . "#" . $matches[2]);

                // ルーティングルールがマッチした場合は抜ける
                return true;
            }
        }

        return false;
    }

    /**
     * 静的ファイルパスを解決する
     */
    private function resolveStaticFilePath()
    {
        $staticFile = $this->applicationInfo->applicationRoot . "/app/views/" . $this->applicationInfo->publicDir . $this->request->pathInfo;

        if (is_file($staticFile)) {
            $this->routingContainer->staticFile = $staticFile;
        } elseif (pathinfo($staticFile, PATHINFO_EXTENSION) == 'css') {
            // cssファイル指定かつ存在しない場合で、同ディレクトリ内にlessファイルがあればcssにコンパイルする
            $less = new \lessc();
            $dirpath = dirname($staticFile);
            $filenameWitoutExt = pathinfo($staticFile, PATHINFO_FILENAME);
            $lessFilepath = $dirpath . "/" . $filenameWitoutExt . ".less";
            // lessファイルも見つからない場合はエラー
            if (!file_exists($lessFilepath)) {
                $this->logger->error("The file of css has been specified, but not found even file of less:" . $lessFilepath);

                return;
            }
            if (@$less->checkedCompile($lessFilepath, $staticFile)) {
                if (is_file($staticFile)) {
                    $this->routingContainer->staticFile = $staticFile;
                } else {
                    $this->logger->error("Failed to file create, cause parmission denied: " . $dirpath);
                }
            }
        }
    }

    /**
     * コントローラを設定する
     * @param string コントローラ文字列
     */
    private function setController($controller)
    {
        if (isset($controller)) {
            $this->routingContainer->pageName = $this->snake2ucamel($controller);
            $this->routingContainer->controller = $this->snake2ucamel($controller) . "Controller";
        }
    }

    /**
     * アクションを設定する
     * @param string アクション文字列
     */
    private function setAction($action)
    {
        if (isset($action)) {
            $this->routingContainer->action = $this->snake2lcamel($action);
        }
    }
}
