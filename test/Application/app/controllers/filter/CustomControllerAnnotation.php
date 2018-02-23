<?php
namespace WebStream\Annotation\Attributes\Custom;

use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Base\IExtension;
use WebStream\Annotation\Base\IMethod;
use WebStream\Annotation\Base\IRead;
use WebStream\Container\Container;

/**
 * @Annotation
 * @Target("METHOD")
 */
class CustomControllerAnnotation extends Annotation implements IMethod, IRead, IExtension
{
    private $injectAnnotation;

    /**
     * {@inheritdoc}
     */
    public function onInject(array $injectAnnotation)
    {
        $this->injectAnnotation = $injectAnnotation;
    }

    /**
     * {@inheritdoc}
     */
    public function getAnnotationInfo(): array
    {
        return $this->injectAnnotation;
    }

    /**
     * {@inheritdoc}
     */
    public function onMethodInject(IAnnotatable $instance, \ReflectionMethod $method, Container $container)
    {
        echo $this->injectAnnotation['name'];
    }
}
