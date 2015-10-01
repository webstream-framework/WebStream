<?php
namespace WebStream\Core;

use WebStream\Delegate\Resolver;
use WebStream\Module\Container;
use WebStream\Module\Utility;
use WebStream\Module\Logger;
use WebStream\Module\PropertyProxy;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;

/**
 * CoreService
 * @author Ryuichi TANAKA.
 * @since 2011/09/11
 * @version 0.4.1
 */
class CoreService implements CoreInterface
{
    use Utility;
    use PropertyProxy;

    /**
     * @var Container コンテナ
     */
    private $container;

    /**
     * @var array<mixed> カスタムアノテーション
     */
    protected $annotation;

    /**
     * {@inheritdoc}
     */
    public function __construct(Container $container)
    {
        Logger::debug("Service start.");
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        Logger::debug("Service end.");
        $this->__clear();
    }

    /**
     * 初期化処理
     * @Inject
     * @Filter(type="initialize")
     */
    public function __initialize(Container $container)
    {
        $coreDelegator = $container->coreDelegator;
        $pageName = $coreDelegator->getPageName();
        $resolver = new Resolver($container);
        $this->{$pageName} = $resolver->runModel();
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
     * Controllerから存在しないメソッドが呼ばれたときの処理
     * @param string メソッド名
     * @param array 引数の配列
     * @return 実行結果
     */
    final public function __call($method, $arguments)
    {
        $coreDelegator = $this->container->coreDelegator;
        $pageName = $coreDelegator->getPageName();

        return $this->{$pageName}->run($method, $arguments);
    }
}
