<?php
namespace WebStream;
/**
 * CoreControllerクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/11
 */
class CoreController extends CoreBase {
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
     * @param Object DIコンテナ
     */
    final public function __construct(Container $container) {
        parent::__construct($container);
        $this->request  = $container->request;
        $this->response = $container->response;
        $this->view = $this->__getView();
    }

    /**
     * 存在しないメソッドへのアクセスを制御
     * @param String メソッド名
     * @param Array 引数
     */
    final public function __call($method, $arguments) {
        if (!$this->__callResponse($method, $arguments)) {
            $class = $this->__toString();
            throw new MethodNotFoundException("${class}#${method} is not defined.");
        }
    }

    /**
     * レスポンスを送出する
     * @param String メソッド名
     * @param Array 引数
     * @return Boolean レスポンス送出結果
     */
    final public function __callResponse($methodName, $args) {
        if (method_exists($this->response, $methodName)) {
            call_user_func_array(array($this->response, $methodName), $args);
            return true;
        }
        return false;
    }

    /**
     * Viewのメソッドを呼び出す
     * @param String メソッド名
     * @param Array 引数
     */
    final public function __callView($methodName, $args = array()) {
        call_user_func_array(array($this->view, $methodName), $args);
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
        $token = Utility::getCsrfTokenKey();
        $session_token = $this->session->get($token);
        $request_token = null;
        $isExistParams = false;

        // セッションにCSRFトークンがセットされている場合、チェックを実行する
        if (isset($session_token)) {
            // CSRFトークンはワンタイムなので削除する
            $this->session->delete($token);
            if ($this->request->isPost()) {
                $request_token = $this->request->post($token);
                $isExistParams = count($this->request->getPOST()) >= 1;
            }
            else if ($this->request->isGet()) {
                $request_token = $this->request->get($token);
                $isExistParams = count($this->request->getGET()) >= 1;
            }
            // POSTパラメータが存在し、かつ、CSRFトークンが一致しない場合はCSRFエラーとする
            if ($session_token !== $request_token && $isExistParams) {
                throw new CsrfException("Sent invalid CSRF token");
            }
        }
    }
    
    /**
     * Serviceクラスのインスタンスをロードする
     * @param String Serviceクラス名
     */
    final private function __load() {
        // Serviceクラスインスタンスを取得
        $service = $this->__getService();
        // Modelクラスインスタンスを取得
        $model = $this->__getModel();

        if ($service) {
            $this->{$this->__pageName} = $service;
        }
        else if ($model) {
            $this->{$this->__pageName} = $model;
        }
        else {
            $serviceClass = $this->__page() . 'Service';
            $modelClass = $this->__page() . 'Model';
            $errorMsg = "$serviceClass and $modelClass is not defined.";
            $this->{$this->__pageName} = new ServiceModelClassNotFoundException($errorMsg);
        }
    }
}
