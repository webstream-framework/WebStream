<?php
namespace WebStream\Annotation\Attributes\Custom;

use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Base\IMethods;
use WebStream\Annotation\Base\IRead;
use WebStream\Container\Container;

/**
 * @Annotation
 * @Target("METHOD")
 */
class CustomControllerAnnotation extends Annotation implements IMethods
{
    /**
     * {@inheritdoc}
     */
    public function onInject(array $injectAnnotation)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onMethodInject(IAnnotatable $instance, \ReflectionMethod $method, Container $container)
    {
        echo "owata";
    }
}
