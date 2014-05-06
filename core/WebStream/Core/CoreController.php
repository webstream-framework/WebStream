<?php
namespace WebStream\Core;

use WebStream\Module\Utility;
use WebStream\Module\Logger;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;
use WebStream\Module\Container;
use WebStream\Exception\Extend\ClassNotFoundException;
use WebStream\Exception\Extend\CsrfException;

/**
 * CoreControllerクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/11
 * @version 0.4.2
 */
class CoreController implements CoreInterface
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
     * Override
     */
    final public function __construct(Container $container)
    {
        Logger::debug("Controller start.");
        $this->request   = $container->request;
        $this->response  = $container->response;
        $this->session   = $container->session;
        $this->coreDelegator = $container->coreDelegator;
    }

    /**
     * Override
     */
    public function __destruct()
    {
        Logger::debug("Controller end.");
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
     * @Filter(type="initialize")
     */
    final public function __initialize()
    {
        $this->__csrfCheck();
        $this->__load();
    }

    /**
     * Model/Serviceオブジェクトを返却する
     * @return object Model/Serviceオブジェクト
     */
    final public function __model()
    {
        return $this->{$this->coreDelegator->getPageName()};
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
