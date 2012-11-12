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
    
    /**
     * Controllerクラス全体の初期化
     */
    final public function __construct() {
        $this->page_name = $this->page();
        $this->view = new CoreView($this->page_name);
    }

    /**
     * Controllerで使用する処理の初期化
     */
    final public function initialize() {
        $this->session = Session::start();
        $this->request = new Request();
        $this->csrfCheck();
        $this->load();
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
     * CSRFトークンをチェックする
     */
    final private function csrfCheck() {
        Security::isCsrfCheck();
    }
    
    /**
     * CSRF対策処理を有効にする
     */
    final public function enableCsrf() {
        $this->view->enableCsrf();
    }
    
    /**
     * Serviceクラスのインスタンスをロードする
     * @param String Serviceクラス名
     */
    final private function load() {
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
     * テンプレートファイルを描画する
     * 
     */
    final public function render2($template, $templates, $params = array(), $mime = "html") {
        $this->view->templates($templates);
        $this->view->render($template, $params, $mime);
    }

    final public function layout2($template, $templates, $params = array(), $mime = "html") {
        $this->view->templates($templates);
        $this->view->layout($template, $params, $mime);
    }
    
    /**
     * レイアウトファイルを描画する
     * @param String テンプレートファイル名
     * @param Hash 埋め込みパラメータ
     */
    final protected function layout($template, $params = array()) {
        $this->view->layout($template, $params);
    }

    /**
     * テンプレートファイルを描画する
     * @param String テンプレートファイル名
     * @param Hash 埋め込みパラメータ
     */
    final protected function render($template, $params = array(), $mime = "html") {
        $this->view->render($template, $params, $mime);
    }
    
    /**
     * テンプレートファイルでJSONを描画する
     * @param Hash 埋め込みパラメータ
     */
    final protected function render_json($params) {
        $this->view->json($params);
    }

    /**
     * テンプレートファイルでJSONPを描画する
     * @param Hash 埋め込みパラメータ
     * @param String コールバック関数
     */
    final protected function render_jsonp($params, $callback) {
        $this->view->jsonp($params, $callback);
    }
    
    /**
     * テンプレートファイルでXMLを描画する
     * @param String テンプレートファイル名
     * @param Hash 埋め込みパラメータ
     */
    final protected function render_xml($template, $params = array()) {
        $this->render($template, $params, "xml");
    }
    
    /**
     * テンプレートファイルでRSSを描画する
     * @param String テンプレートファイル名
     * @param Hash 埋め込みパラメータ
     */
    final protected function render_rss($template, $params = array()) {
        $this->render($template, $params, "rss");
    }
    
    /**
     * テンプレートファイルでATOMを描画する
     * @param String テンプレートファイル名
     * @param Hash 埋め込みパラメータ
     */
    final protected function render_atom($template, $params = array()) {
        $this->render($template, $params, "atom");
    }
    
    /**
     * エラーページ用HTMLを描画する
     * @param String 表示内容
     */
    final protected function render_error($content) {
        $this->view->error($content);
    }
    
    /**
     * リダイレクトする
     * @param String ドキュメントルートからの相対パス
     */
    final protected function redirect($path) {
        $this->move(301, $path);
    }
    
    /**
     * アクセス権限のない処理
     */
    final protected function forbidden() {
        $this->move(403);
    }
    
    /**
     * 静的ファイルを描画する
     * @param String ファイルパス
     */
    final public function render_file($filepath) {
        $this->view->renderPublicFile($filepath);
    }
    
    /**
     * デフォルト画面を描画する
     * @param Integer ステータスコード
     * @param String 遷移パス
     */
    final public function move($status_code, $path = null) {
        switch ($status_code) {
        case 301:
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: " . $path);
            break;
        case 400:
            header("HTTP/1.1 400 Bad Request");
            $this->render_error("400 Bad Request");
            break;
        case 401:
            header("WWW-Authenticate: Basic realm='Private page'");
            header("HTTP/1.1 401 Unauthorized");
            $this->render_error("401 Unauthorized");
            break;
        case 403:
            header("HTTP/1.1 403 Forbidden");
            $this->render_error("403 Forbidden");
            break;
        case 404:
            header("HTTP/1.1 404 Not Found");
            $this->render_error("404 Not Found");
            break;
        case 405:
            header("HTTP/1.1 405 Method Not Allowed");
            $this->render_error("405 Method Not Allowed");
            break;
        case 422:
            header("HTTP/1.1 422 Unprocessable Entity");
            $this->render_error("422 Unprocessable Entity");
            break;
        case 500:
            header("HTTP/1.1 500 Internal Server Error");
            $this->render_error("500 Internal Server Error");
            break;
        default:
            throw new ConnectionException("Unknown status code: " . $status_code);
        }
        Logger::info("HTTP access occured: status code ${status_code}");
        exit;
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
