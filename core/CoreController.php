<?php
namespace WebStream;
/**
 * CoreControllerクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/11
 */
class CoreController {
    /** ページ名 */
    private $page_name;
    /** view */
    private $view;
    /** セッション */
    protected $session;
    /** リクエスト */
    protected $request;
    /** レスポンス */
    private $response;
    
    /**
     * Controllerクラス全体の初期化
     */
    final public function __construct() {
        $this->page_name = $this->page();
        $this->request = Request::getInstance();
        $this->response = Response::getInstance();
        $this->view = new CoreView($this->page_name);
    }

    /**
     * 存在しないメソッドへのアクセスを制御
     * @param String メソッド名
     * @param Array 引数
     */
    final public function __call($method, $arguments) {
        $class = get_class($this);
        throw new MethodNotFoundException("${class}#${method} is not defined.");
    }

    /**
     * Controllerで使用する処理の初期化
     */
    final public function __initialize() {
        $this->session = Session::start();
        $this->__csrfCheck();
        $this->__load();
    }
    
    /**
     * CSRFトークンをチェックする
     */
    final private function __csrfCheck() {
        Security::isCsrfCheck();
    }
    
    /**
     * CSRF対策処理を有効にする
     */
    final public function __enableCsrf() {
        $this->view->enableCsrf();
    }

    /**
     * テンプレートリスト情報をViewに通知
     * @param Hash テンプレートリスト情報
     */
    final public function __templates($templates) {
        $this->view->__templates($templates);
    }

    /**
     * レンダリングメソッドリスト情報を設定
     * @param Hash レンダリングメソッドリスト情報
     */
    final public function __renderMethods($methods) {
        $this->view->__renderMethods($methods);
    }
    
    /**
     * Serviceクラスのインスタンスをロードする
     * @param String Serviceクラス名
     */
    final private function __load() {
        $service_class = $this->page_name . "Service";
        $model_class = $this->page_name . "Model";
        // Serviceクラスが存在する場合、Serviceクラスをロード
        if (import(STREAM_APP_DIR . "/services/" . $service_class)) {
            $class = new \ReflectionClass(STREAM_CLASSPATH . $service_class);
            $service_ins = $class->newInstance();
            $this->{$this->page_name} = $service_ins;
        }
        // Serviceクラスが存在しない場合、Modelクラスをロードする
        else if (import(STREAM_APP_DIR . "/models/" . $model_class)) {
            $class = new \ReflectionClass(STREAM_CLASSPATH . $model_class);
            $model_ins = $class->newInstance();
            $this->{$this->page_name} = $model_ins;
        }
        // ServiceもModel存在しない場合
        else {
            $errorMsg = "${service_class} and ${model_class} is not defined.";
            $this->{$this->page_name} = new ServiceModelClassNotFoundException($errorMsg);
        }
    }
    
    /**
     * レイアウトファイルを描画する
     * @param String テンプレートファイル名
     * @param Hash 埋め込みパラメータ
     */
    final public function __layout($template, $params = array(), $mime = "html") {
        $this->view->__layout($template, $params);
    }

    /**
     * テンプレートファイルを描画する
     * @param String テンプレートファイル名
     * @param Hash 埋め込みパラメータ
     */
    final public function __render($template, $params = array(), $mime = "html") {
        $this->view->__render($template, $params, $mime);
    }
    
    /**
     * テンプレートファイルでJSONを描画する
     * @param Hash 埋め込みパラメータ
     */
    final public function __render_json($params) {
        $this->view->__json($params);
    }

    /**
     * テンプレートファイルでJSONPを描画する
     * @param Hash 埋め込みパラメータ
     * @param String コールバック関数
     */
    final public function __render_jsonp($params, $callback) {
        $this->view->__jsonp($params, $callback);
    }
    
    /**
     * エラーページ用HTMLを描画する
     * @param String 表示内容
     */
    final protected function __render_error($content) {
        $this->view->__error($content);
    }
    
    /**
     * リダイレクトする
     * @param String ドキュメントルートからの相対パス
     */
    final protected function redirect($path) {
        $this->response->movePermanently($path);
    }
    
    /**
     * アクセス権限のない処理
     */
    final protected function forbidden() {
        $this->response->forbidden();
    }
    
    /**
     * 静的ファイルを描画する
     * @param String ファイルパス
     */
    final public function __render_file($filepath) {
        $this->view->renderPublicFile($filepath);
    }
    
    /**
     * ステータスコードに応じた画面を描画する
     * @param Integer ステータスコード
     */
    final public function __move($statusCode) {
        $this->response->move($statusCode);
    }

    /**
     * ページ名を取得する
     * @return String ページ名
     */
    final private function page() {
        $page_name = null;
        $class_path = explode('\\', get_class($this));
        $class_name = end($class_path);
        if (preg_match('/(.*)Controller$/', $class_name, $matches)) {
            $page_name = $matches[1];
        }
        return $page_name;
    }
}
