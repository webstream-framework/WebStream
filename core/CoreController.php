<?php
/**
 * CoreControllerクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/11
 */
class CoreController {
    /** appディレクトリ */
    private $app_dir;
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
     * @param String appディレクトリパス
     * @param String ページ名
     */
    final public function __construct($app_dir, $page_name) {
        $this->app_dir = $app_dir;
        $this->page_name = $page_name;
        $this->view = new CoreView($app_dir, $page_name);
        $this->session = Session::start();
        $this->request = new Request();
        $this->csrf();
        $this->load(ucfirst($page_name) . "Service");
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
    final private function load($service_name) {
        // Serviceクラスをインポート
        $service_ins = null;
        if (import($this->app_dir . "/services/" . $service_name)) {
            $class = new ReflectionClass($service_name);
            $service_ins = $class->newInstance($this->app_dir, $this->page_name);
        }
        $this->{ucfirst($this->page_name)} = $service_ins;
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
     * before filterの空実装
     */
    public function before() {}
    
    /**
     * after filterの空実装
     */
    public function after() {}
}
