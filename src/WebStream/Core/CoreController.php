<?php
namespace WebStream\Core;

use WebStream\Delegate\Resolver;
use WebStream\DI\Injector;
use WebStream\Container\Container;
use WebStream\Annotation\Attributes\Filter;
use WebStream\Annotation\Base\IAnnotatable;

/**
 * CoreControllerクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/11
 * @version 0.4.2
 */
class CoreController implements CoreInterface, IAnnotatable
{
    use Injector;

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
     * @var CoreDelegator コアデリゲータ
     */
    private $coreDelegator;

    /**
     * @var array<mixed> カスタムアノテーション
     */
    protected $annotation;

    /**
     * @var LoggerAdapter ロガー
     */
    protected $logger;

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->logger->debug("Controller end.");
        $this->__clear();
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
     * {@inheritdoc}
     */
    public function __customAnnotation(array $annotation)
    {
        $this->annotation = $annotation;
    }

    /**
     * {@inheritdoc}
     * @Filter(type="initialize")
     */
    public function __initialize(Container $container)
    {
        $pageName = $this->coreDelegator->getPageName();
        $resolver = new Resolver($container);
        $this->{$pageName} = $resolver->runService() ?: $resolver->runModel();
    }
}
