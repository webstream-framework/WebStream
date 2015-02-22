<?php
namespace WebStream\Test\TestData\Sample\App\Annotation;

use WebStream\Core\CoreInterface;
use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IClass;
use WebStream\Annotation\Base\IRead;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Module\Container;

/**
 * @Annotation
 * @Target("CLASS")
 */
class CustomAnnotation5 extends Annotation implements IClass, IRead
{
    /**
     * @var AnnotationContainer アノテーションコンテナ
     */
    private $annotaion;

    private $data;

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
    public function onInjected()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function onClassInject(CoreInterface &$instance, Container $container, \ReflectionClass $class)
    {
        $this->data = "kashikoi";
    }
}
