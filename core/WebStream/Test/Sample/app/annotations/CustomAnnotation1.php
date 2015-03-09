<?php
namespace WebStream\Test\TestData\Sample\App\Annotation;

use WebStream\Core\CoreInterface;
use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IMethod;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Module\Container;

/**
 * @Annotation
 * @Target("METHOD")
 */
class CustomAnnotation1 extends Annotation implements IMethod
{
    /**
     * @var AnnotationContainer アノテーションコンテナ
     */
    private $annotation;

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
        // カスタムアノテーションはここで処理を完結させる必要がある
        if ($this->annotation->exception) {
            throw new \Exception("custom annotation exception");
        }
    }
}
