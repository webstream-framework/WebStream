<?php
/**
 * ルーティングクラス
 * @author Ryuichi TANAKA.
 * @since 2011/08/19
 */
class Router {
    /** ルーティングルール */
    private static $rules;
    /** PATH_INFO */
    private $path_info;
    /** ルーティング解決後の各パラメータ */
    private $route;
    
    /**
     * コンストラクタ
     * @params Array ルーティングルール
     */
    public function __construct() {
        $request = new Request();
        $this->path_info = $request->getPathInfo();
        $this->validate();
        $this->resolve();
    }
    
    /**
     * ルーティングルールをセットする
     * @param Hash ルーティングルール定義
     */
    public static function setRule($rules) {
        self::$rules = $rules;
    }
    
    /**
     * 禁止されたルーティングルールをチェックする
     */
    private function validate() {
        // パス定義部分('/xxx')は禁止の定義がされた時点でエラーとする
        // CA部分('controller#action')はパスにアクセスされたときにチェックする
        foreach (self::$rules as $path => $ca) {
            // 静的ファイルへのパスがルーティングルールに定義された場合
            // パス定義された時点で弾く
            if (preg_match('/\/(img|js|css)(?:$|\/)/', $path)) {
                throw new Exception("Include the prohibit routing path: " . $path);
            }
            
            // 許可したルーティングパス定義に合っていなければ弾く
            if (!preg_match('/^\/{1}(?:$|[a-zA-Z]{1}[a-zA-Z0-9.-_\/]*$)/', $path)) {
                throw new Exception("Invalid path defintion: " . $path);
            }
            // ルールとURLがマッチした場合に動的にチェックを掛ける
            // パスがマッチしたときにアクション名をチェックし、その時点で弾く
            if ($this->path_info === $path) {
                // ルーティング定義(Controller#Action)が正しい場合
                // _(アンダースコア)は許可するが、２回以上の連続の場合、末尾につく場合は許可しない
                // NG例：my__blog, my_blog_
                if (preg_match('/^(?:([a-z]{1}(?:_(?=[a-z])|[a-z0-9])+))#(?:([a-z]{1}(?:_(?=[a-z])|[a-z0-9])+))$/', $ca, $matches)) {
                    // アクション名にrender, errorが指定された場合
                    // 正しいルーティング定義のとき、かつ、render,errorが指定された場合にエラーとする
                    if (preg_match('/#((?:re(?:direct|nder)|l(?:ayout|oad)|before|after)$)/', $ca, $matches)) {
                        throw new Exception("Invalid action definition: " . $matches[1]);
                    }
                }
                // ルーティング定義(Controller#Action)が正しくない場合
                else {
                    throw new Exception("Invalid controller#action definition: " . $ca);
                }
            }
        }
    }
    
    /**
     * ルーティングルールを各パスに分解する
     * @param Array ルーティングルール
     */
    public function resolve() {
        // ルーティングパラメータ
        $routes = array();
        // ルーティングルールからController、Actionを取得
        foreach (self::$rules as $path => $ca) {
            $route = array();
            $tokens = explode("/", ltrim($path, "/"));
            $route["params"] = array();
            $key_list = array();

            // URLに静的ファイルへのパスが指定された場合
            if (preg_match('/\/(?:img|js|css)(?:$|\/)/', $this->path_info)) {
                $route["staticFile"] = $this->path_info;
            }
            // それ以外
            else {
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
                $expantion_path = $path;
                if ($this->path_info !== $path) {
                    // プレースホルダと実URLをひもづける
                    $path_pattern = "/^\/" . implode("\/", $tokens) . "$/";
                    if (preg_match($path_pattern, $this->path_info, $matches)) {
                        for ($j = 1; $j < count($matches); $j++) {
                            $key = $key_list[$j - 1];
                            $route["params"][$key] = $matches[$j];
                            // プレースホルダを一時展開する
                            $expantion_path = preg_replace('/:[a-zA-Z0-9]+/', $matches[$j], $expantion_path, 1);
                        }
                    }
                }
                // プレースホルダを展開済みのパス定義が完全一致したときはController、Actionを展開する
                if ($this->path_info === $expantion_path &&
                    preg_match('/^(?:([a-z]{1}(?:_(?=[a-z])|[a-z0-9])+))#(?:([a-z]{1}(?:_(?=[a-z])|[a-z0-9])+))$/', $ca, $matches)) {
                    $route["controller"] = $matches[1];
                    $route["action"] = $matches[2];
                }
            }

            // ルーティングルールがマッチした場合は抜ける
            if ((isset($route["controller"]) && isset($route["action"])) || isset($route["staticFile"])) {
                $this->route = $route;
                break;
            }
        }
    }
    
    /**
     * コントローラ名を返却する
     * @return String コントローラ名
     */
    public function controller() {
        return isset($this->route["controller"]) ? 
            $this->route["controller"] : null;
    }
    
    /**
     * アクション名を返却する
     * @return String アクション名
     */
    public function action() {
        return isset($this->route["action"]) ? 
            $this->route["action"] : null;
    }
    
    /**
     * パラメータを返却する
     * @return Array ラメータ
     */
    public function params() {
        return isset($this->route["params"]) ? 
            $this->route["params"] : null;
    }
    
    /**
     * 静的ファイルパスを返却する
     * @return String 静的ファイルパス
     */
    public function staticFile() {
        return $this->route["staticFile"];
    }
}
