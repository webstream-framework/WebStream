<?php
namespace WebStream\Annotation\Base;

use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Annotation\Container\ContainerFactory;
use WebStream\DI\Injector;

/**
 * Annotaion
 * @author Ryuichi TANAKA.
 * @since 2014/05/11
 * @version 0.7
 */
abstract class Annotation
{
    use Injector;

    /**
     * constructor
     * @param array<string> アノテーションリスト
     */
    public function __construct(array $annotations = [])
    {
        $factory = new ContainerFactory($annotations);
        $this->onInject($factory->createContainer());
    }

    /**
     * Add injected log
     * @param object アノテーションクラスオブジェクト
     */
    protected function injectedLog(Annotation $class)
    {
        $this->logger->debug("@" . get_class($class) . " injected.");
    }

    /**
     * Injected event
     * @param AnnotationContainer アノテーションコンテナ
     */
    abstract public function onInject(AnnotationContainer $container);
}
