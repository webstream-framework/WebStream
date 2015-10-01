<?php
namespace WebStream\Annotation\Base;

use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Annotation\Container\ContainerFactory;

/**
 * Annotaion
 * @author Ryuichi TANAKA.
 * @since 2014/05/11
 * @version 0.4
 */
abstract class Annotation
{
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
     * Injected event
     * @param AnnotationContainer アノテーションコンテナ
     */
    abstract public function onInject(AnnotationContainer $container);
}
