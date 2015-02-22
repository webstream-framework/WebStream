<?php
namespace WebStream\Core;

use WebStream\Module\Utility;
use WebStream\Module\Logger;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;
use WebStream\Module\Container;
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

    /**
     * @var Session セッション
     */
    protected $session;

    /**
     * @var Request リクエスト
     */
    protected $request;

    /**
     * @var Response レスポンス
     */
    private $response;

    /**
     * @var array<mixed> アノテーション
     */
    protected $annotation;

    /**
     * @var Container コンテナ
     */
    private $container;

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
        $this->coreDelegator->getView()->__file($filepath);
    }

    /**
     * カスタムアノテーション情報を設定する
     * @param array<mixed> カスタムアノテーション情報
     */
    final public function __customAnnotation(array $annotation)
    {
        $this->annotation = $annotation;
    }

    /**
     * Controllerで使用する処理の初期化
     * @Inject
     * @Filter(type="initialize")
     */
    final public function __initialize()
    {
        // CSRF
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

        // Service/Modelロード
        $pageName = $this->coreDelegator->getPageName();
        $this->{$pageName} = $this->coreDelegator->getService() ?: $this->coreDelegator->getModel();
    }
}
