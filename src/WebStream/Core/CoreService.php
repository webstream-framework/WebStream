<?php
namespace WebStream\Core;

use WebStream\Delegate\Resolver;
use WebStream\DI\Injector;
use WebStream\Container\Container;
use WebStream\Annotation\Attributes\Filter;
use WebStream\Annotation\Base\IAnnotatable;

/**
 * CoreService
 * @author Ryuichi TANAKA.
 * @since 2011/09/11
 * @version 0.4.1
 */
class CoreService implements CoreInterface, IAnnotatable
{
    use Injector;

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
     * {@inheritdoc}
     */
    public function __destruct()
    {
        $this->logger->debug("Service end.");
        $this->__clear();
    }

    /**
     * {@inheritdoc}
     * @Filter(type="initialize")
     */
    public function __initialize(Container $container)
    {
        $pageName = $this->coreDelegator->getPageName();
        $resolver = new Resolver($container);
        $this->{$pageName} = $resolver->runModel();
    }

    /**
     * {@inheritdoc}
     */
    public function __customAnnotation(array $annotation)
    {
        $this->annotation = $annotation;
    }

    /**
     * Controllerから存在しないメソッドが呼ばれたときの処理
     * @param string メソッド名
     * @param array 引数の配列
     * @return 実行結果
     */
    final public function __call($method, $arguments)
    {
        $pageName = $this->coreDelegator->getPageName();

        return $this->{$pageName}->run($method, $arguments);
    }
}
