<?php
namespace WebStream\Test\TestData\Sample\App\Annotation;

use WebStream\Core\CoreInterface;
use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IClass;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Module\Container;

/**
 * @Annotation
 * @Target("CLASS")
 */
class CustomAnnotation4 extends Annotation implements IClass
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
    public function onClassInject(CoreInterface &$instance, Container $container, \ReflectionClass $class)
    {
        echo "kke";
    }
}
