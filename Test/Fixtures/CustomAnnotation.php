<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Base\IMethods;
use WebStream\Container\Container;

/**
 * @Annotation
 * @Target("METHOD")
 */
class CustomAnnotation extends Annotation implements IMethods
{
    /**
     * @var array<string> 注入アノテーション情報
     */
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
    public function onMethodInject(IAnnotatable $instance, \ReflectionMethod $method, Container $container)
    {
        echo $this->injectAnnotation['name'];
    }
}
