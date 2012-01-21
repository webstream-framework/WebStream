<?php
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
        $this->view = new CoreView(Utility::camel2snake($this->page_name));
        $this->session = Session::start();
        $this->request = new Request();
        $this->csrf();
        $this->load();
    }
    
    /**
     * CSRFトークンをチェックする
     */
    final private function csrf() {
        // POSTメソッドによるアクセスの場合
        if ($this->request->isPost()) {
            $session_token = $this->session->get(Utility::getCsrfTokenKey());
            $request_token = $this->request->post(Utility::getCsrfTokenKey());
            $this->session->delete(Utility::getCsrfTokenKey());
            // セッションにCSRFトークンが存在し、かつ、送信されてきたCSRFトークンが
            // 一致しない場合はCSRFエラーとする
            if (!empty($session_token) && $session_token !== $request_token) {
                throw new CsrfException("Sent invalid CSRF token");
            }
        }
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
            $class = new ReflectionClass($service_class);
            $service_ins = $class->newInstance();
            $this->{$this->page_name} = $service_ins;
        }
        // Serviceクラスが存在しない場合、Modelクラスをロードする
        else if (import(STREAM_APP_DIR . "/models/" . $model_class)) {
            $class = new ReflectionClass($model_class);
            $model_ins = $class->newInstance();
            $this->{$this->page_name} = $model_ins;
        }
        // ServiceもModel存在しない場合
        else {
            $this->{$this->page_name} = 
                new ServiceModelClassNotFoundException("${service_class} and ${model_class} is not defined.");
        }
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
    final protected function render_json($params, $callback = null) {
        $this->view->json($params, $callback);
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
     * リダイレクトする
     * @param String ドキュメントルートからの相対パス
     */
    final protected function redirect($path) {
        $this->view->redirect(STREAM_BASE_URI . $path);
    }
    
    /**
     * ページ名を取得する
     * @return String ページ名
     */
    final private function page() {
        $page_name = null;
        if (preg_match('/(.*)Controller$/', get_class($this), $matches)) {
            $page_name = $matches[1];
        }
        return $page_name;
    }
    
    /**
     * before filterの空実装
     */
    public function before() {}
    
    /**
     * after filterの空実装
     */
    public function after() {}
}
