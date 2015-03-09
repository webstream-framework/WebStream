<?php
namespace WebStream\Test\TestData\Sample\App\Annotation;

use WebStream\Core\CoreInterface;
use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IProperty;
use WebStream\Annotation\Base\IRead;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Module\Container;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class CustomAnnotation8 extends Annotation implements IProperty, IRead
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
    public function onPropertyInject(CoreInterface &$instance, Container $container, \ReflectionProperty $property)
    {
        $this->data = "sanchou attack";
    }
}
