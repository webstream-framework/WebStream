<?php
/**
 * CoreControllerクラス
 * @author Ryuichi TANAKA.
 * @since 2012/04/28
 */
class CoreHelper {
    /** ページ名 */
    private $page_name;
    /** Helperインスタンス */
    private $instance;
    
    /**
     * コンストラクタ
     * @param String ページ名
     */
    public function __construct($page_name = null) {
        $this->page_name = $page_name;
        $this->instance = $this->getHelper();
    }
    
    /**
     * Helperクラスを起動するための処理
     * @param String メソッド名
     * @param Array 引数の配列
     */
    public function __call($method, $arguments) {
        // _[a-z]を[A-Z]に置換する
        $method = preg_replace_callback('/_(?=[a-z])(.+?)/', create_function(
            '$matches',
            'return ucfirst($matches[1]);'
        ), $method);
        // Helperクラスにメソッドが存在しない場合、例外が派生
        if (method_exists($this->instance, $method) === false) {
            $helper_class = $this->page_name . "Helper";
            throw new MethodNotFoundException("${helper_class}#${method} is not defined.");
        }
        // 引数を安全な値に置換
        for ($i = 0; $i < count($arguments); $i++) {
            $arguments[$i] = safetyOut($arguments[$i]);
        }
        echo call_user_func_array(array($this->instance, $method), $arguments);
    }

    /**
     * Helperクラスのインスタンスを返却する
     * @return Object Helperクラスインスタンス
     */
    final private function getHelper() {
        $helper_class = $this->page_name . "Helper";
        $instance = null;

        // Helperクラスをインポート
        import(STREAM_APP_DIR . "/helpers/AppHelper");
        import(STREAM_APP_DIR . "/helpers/" . $helper_class);
        
        // Helperクラスが存在する場合、Helperクラスをロード
        if (import(STREAM_APP_DIR . "/helpers/" . $helper_class)) {
            $class = new ReflectionClass($helper_class);
            $instance = $class->newInstance();
        }
        
        return $instance;
    }
}
