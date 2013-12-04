<?php
namespace WebStream\Core;

use WebStream\Module\Utility;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;
use WebStream\Annotation\FilterReader;
use WebStream\Annotation\AutowiredReader;
use WebStream\Annotation\TemplateReader;
use WebStream\Annotation\HeaderReader;
use WebStream\Annotation\TemplateCacheReader;
use WebStream\Module\Container;
use WebStream\Exception\ClassNotFoundException;
use WebStream\Exception\MethodNotFoundException;
use WebStream\Exception\CsrfException;

/**
 * CoreControllerクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/11
 * @version 0.4.2
 */
class CoreController
{
    use Utility;

    /** セッション */
    protected $session;
    /** リクエスト */
    protected $request;
    /** レスポンス */
    private $response;
    /** CoreDelegator */
    private $container;

    /**
     * Controllerクラス全体の初期化
     * @param object DIコンテナ
     */
    final public function __construct(Container $container)
    {
        $this->request   = $container->request;
        $this->response  = $container->response;
        $this->session   = $container->session;
        $this->coreDelegator = $container->coreDelegator;
    }

    /**
     * Controller起動時の初期処理
     * @param object リフレクションクラスインスタンス
     * @param string メソッド名
     * @param array 引数
     * @param object コンテナオブジェクト
     */
    final public function __callInitialize($refClass, $action, $params, Container $container)
    {
        // autowired
        $autowired = new AutowiredReader();
        $autowired->read($refClass, null, $container);
        $self = $autowired->getInstance();

        // header
        $header = new HeaderReader();
        $header->read($refClass, $action, $container);
        $mime = $header->getMimeType();

        // filter
        $reader = new FilterReader($self);
        $reader->read($refClass);
        $filter = $reader->getComponent();

        // initialize filter
        $filter->initialize();
        // before filter
        $filter->before();

        // action
        $template = new TemplateReader();
        $template->read($refClass, $action, $container);
        $templateComponent = $template->getComponent();

        if (!method_exists($self, $action)) {
            $class = get_class($self);
            throw new MethodNotFoundException("${class}#${action} is not defined.");
        }

        $data = $self->{$action}($params);
        if ($data === null) {
            $data = [];
        }

        $embed = $templateComponent->getEmbed();
        if (!empty($embed)) {
            $data = array_merge($data, $embed);
        }

        // draw template
        $viewDir = STREAM_ROOT . "/" . STREAM_APP_DIR . "/views";
        $view = $this->coreDelegator->getView();
        $view->draw($viewDir . "/" . $templateComponent->getBase(), $data, $mime);

        $templateCache = new TemplateCacheReader();
        $templateCache->read($refClass, $action);
        $expire = $templateCache->getExpire();

        if ($expire !== null) {
            // create cache
            $pageName = $this->coreDelegator->getPageName();
            $cacheFile = STREAM_CACHE_PREFIX . $this->camel2snake($pageName) . "-" . $this->camel2snake($action);
            $view->cache($cacheFile, ob_get_contents(), $expire);
        }

        // after filter
        $filter->after();
    }

    /**
     * 静的ファイルを読み込む
     * @param string 静的ファイルパス
     */
    final public function __callStaticFile($filepath)
    {
        $view = $this->coreDelegator->getView();
        $view->__file($filepath);
    }

    /**
     * Controllerで使用する処理の初期化
     * @Inject
     * @Filter("Initialize")
     */
    final public function __initialize()
    {
        $this->__csrfCheck();
        $this->__load();
    }

    /**
     * CSRFトークンをチェックする
     */
    final private function __csrfCheck()
    {
        $csrfKey = $this->getCsrfTokenKey();
        $sessionToken = $this->session->get($csrfKey);
        $requestToken = null;

        if (isset($sessionToken)) {
            // CSRFトークンはワンタイムなので削除する
            $this->session->delete($csrfKey);
        }

        if ($this->request->isPost()) {
            $requestToken = $this->request->post($csrfKey);
        } elseif ($this->request->isGet()) {
            $requestToken = $this->request->get($csrfKey);
        }

        // CSRFトークンが送信されているかつサーバのトークンが一致
        // しない場合はエラーとする。
        if ($requestToken !== $sessionToken) {
            throw new CsrfException("Sent invalid CSRF token");
        }
    }

    /**
     * Service/Modelクラスのインスタンスをロードする
     */
    final private function __load()
    {
        $pageName = $this->coreDelegator->getPageName();

        // Serviceクラスインスタンスを取得
        $service = $this->coreDelegator->getService();
        // Modelクラスインスタンスを取得
        $model = $this->coreDelegator->getModel();

        if ($service) {
            $this->{$pageName} = $service;
        } elseif ($model) {
            $this->{$pageName} = $model;
        } else {
            $errorMsg = $pageName . "Service and " . $pageName . "Model is not defined.";
            $this->{$pageName} = new ClassNotFoundException($errorMsg);
        }
    }
}
