<?php
namespace WebStream\Test\TestData\Sample\App\Annotation;

use WebStream\Core\CoreInterface;
use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IMethods;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Module\Container;

/**
 * @Annotation
 * @Target("METHOD")
 */
class CustomAnnotation3 extends Annotation implements IMethods
{
    /**
     * @var AnnotationContainer アノテーションコンテナ
     */
    private $annotaion;

    /**
     * {@inheritdoc}
     */
    public function onInject(AnnotationContainer $annotation)
    {
        $this->annotation = $annotation;
    }

    /**
     * {@inheritdoc}
     */
    public function onMethodInject(CoreInterface &$instance, Container $container, \ReflectionMethod $method)
    {
        echo "nico";
    }
}
