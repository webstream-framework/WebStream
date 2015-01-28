<?php
namespace WebStream\Annotation;

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
     * @var AnnotationContainer アノテーションコンテナ
     */
    protected $annotationContainer;

    /**
     * constructor
     */
    public function __construct($annotations = [])
    {
        $factory = new ContainerFactory($annotations);
        $this->annotationContainer = $factory->createContainer();
        $this->onInject();
    }

    /**
     * Injected event
     */
    abstract public function onInject();

    /**
     * コンテナを返却する
     * @return AnnotationContainer アノテーションコンテナ
     */
    public function getAnnotationContainer()
    {
        return $this->annotationContainer;
    }
}
