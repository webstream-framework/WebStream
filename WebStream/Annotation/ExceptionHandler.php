<?php
namespace WebStream\Annotation;

use WebStream\Core\CoreInterface;
use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Base\IRead;
use WebStream\Annotation\Base\IMethods;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Module\Container;

/**
 * ExceptionHandler
 * @author Ryuichi TANAKA.
 * @since 2013/11/22
 * @version 0.4
 *
 * @Annotation
 * @Target("METHOD")
 */
class ExceptionHandler extends Annotation implements IMethods, IRead
{
    /**
     * @var WebStream\Annotation\Container\AnnotationContainer アノテーションコンテナ
     */
    private $annotation;

    /**
     * @var WebStream\Annotation\Container\AnnotationContainer 注入結果
     */
    private $injectedContainer;

    /**
     * {@inheritdoc}
     */
    public function onInject(AnnotationContainer $annotation)
    {
        $this->annotation = $annotation;
        $this->injectedContainer = new AnnotationContainer();
    }

    /**
     * {@inheritdoc}
     */
    public function onInjected()
    {
        return $this->injectedContainer;
    }

    /**
     * {@inheritdoc}
     */
    public function onMethodInject(IAnnotatable &$instance, Container $container, \ReflectionMethod $method)
    {
        $this->injectedLog($this);

        $exceptions = $this->annotation->value;
        if (!is_array($exceptions)) {
            $exceptions = [$exceptions];
        }

        $this->injectedContainer->exceptions = $exceptions;
        $this->injectedContainer->method = $method;
    }
}
