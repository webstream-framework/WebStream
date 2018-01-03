<?php
namespace WebStream\Annotation\Attributes;

use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Base\IMethods;
use WebStream\Annotation\Base\IRead;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Container\Container;
use WebStream\Exception\Extend\AnnotationException;

/**
 * Filter
 * @author Ryuichi TANAKA.
 * @since 2013/10/20
 * @version 0.4
 *
 * @Annotation
 * @Target("METHOD")
 */
class Filter extends Annotation implements IMethods, IRead
{
    /**
     * @var array<string> 注入アノテーション情報
     */
    private $injectAnnotation;

    /**
     * @var array<string> 読み込みアノテーション情報
     */
    private $readAnnotation;

    /**
     * {@inheritdoc}
     */
    public function onInject(array $injectAnnotation)
    {
        $this->injectAnnotation = $injectAnnotation;
        $this->readAnnotation = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAnnotationInfo(): array
    {
        return $this->readAnnotation;
    }

    /**
     * {@inheritdoc}
     */
    public function onMethodInject(IAnnotatable $instance, \ReflectionMethod $method, Container $container)
    {
        $annotationContainer = new AnnotationContainer();
        foreach ($this->injectAnnotation as $key => $value) {
            $annotationContainer->{$key} = $value;
        }

        $this->readAnnotation = [
            'classpath' => get_class($instance),
            'action' => $container->action,
            'refMethod' => $method,
            'annotation' => $annotationContainer
        ];
    }
}
