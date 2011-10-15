<?php
/**
 * CoreServiceクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/11
 */
class CoreService {
    /** appディレクトリ */
    private $app_dir;
    /** ページ名 */
    private $page_name;
    
    /**
     * Serviceクラス全体の初期化
     * @param String appディレクトリパス
     * @param String ページ名
     */
    final public function __construct($app_dir, $page_name) {
        $this->app_dir = $app_dir;
        $this->page_name = $page_name;
        $model_name = ucfirst($page_name) . "Model";
        $this->load($model_name);
        // ライブラリをロード
        importAll("app/libraries");
    }
    
    /**
     * Modelクラスのインスタンスをロードする
     * @param String Modelクラス名
     */
    final private function load($model_name) {
        // Modelクラスをインポート
        $model_ins = null;
        if (import($this->app_dir . "/models/" . $model_name)) {
            $class = new ReflectionClass($model_name);
            $model_ins = $class->newInstance();
        }
        $this->{ucfirst($this->page_name)} = $model_ins;
    }
}
