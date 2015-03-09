<?php
namespace WebStream\Test\TestData\Sample\App\Annotation;

use WebStream\Core\CoreInterface;
use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IMethod;
use WebStream\Annotation\Base\IRead;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Module\Container;

/**
 * @Annotation
 * @Target("METHOD")
 */
class CustomAnnotation2 extends Annotation implements IMethod, IRead
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
    public function onMethodInject(CoreInterface &$instance, Container $container, \ReflectionMethod $method)
    {
        $this->data = "chunchun";
    }
}
