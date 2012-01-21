<?php
/**
 * CoreServiceクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/11
 */
class CoreService {
    /** ページ名 */
    private $page_name;
    
    /**
     * Controllerから存在しないメソッドが呼ばれたときの処理
     * @param String メソッド名
     * @param Array 引数の配列
     * @return 実行結果
     */
    final public function __call($method, $arguments) {
        // Modelクラス両方にメソッドが存在しなければエラー
        if ($this->page_name === null || method_exists($this->{$this->page_name}, $method) === false) {
            $class = get_class($this);
            throw new MethodNotFoundException("${class}#${method} is not defined.");
        }
        return call_user_func_array(array($this->{$this->page_name}, $method), $arguments);
    }
    
    /**
     * Serviceクラス全体の初期化
     * @param String ページ名
     */
    final public function __construct() {
        $this->page_name = $this->page();
        $this->load();
        // ライブラリをロード
        importAll(STREAM_APP_DIR . "/libraries");
    }
    
    /**
     * Modelクラスのインスタンスをロードする
     * @param String Modelクラス名
     */
    final private function load() {
        $model_name = $this->page_name . "Model";
        // Modelクラスをインポート
        $model_ins = null;
        if (import(STREAM_APP_DIR . "/models/" . $model_name)) {
            $class = new ReflectionClass($model_name);
            $model_ins = $class->newInstance();
        }
        $this->{$this->page_name} = $model_ins;
    }
    
    /**
     * ページ名を取得する
     * @return String ページ名
     */
    final private function page() {
        $page_name = null;
        if (preg_match('/(.*)Service$/', get_class($this), $matches)) {
            $page_name = $matches[1];
        }
        return $page_name;
    }
}
