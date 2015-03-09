<?php
namespace WebStream\Annotation;

use WebStream\Core\CoreInterface;
use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IRead;
use WebStream\Annotation\Base\IMethods;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Module\Logger;
use WebStream\Module\Container;

/**
 * Filter
 * @author Ryuichi TANAKA.
 * @since 2013/10/20
 * @version 0.4
 *
 * @Annotation
 * @Target("METHOD")
 */
class Filter extends Annotation implements IMethods, IRead
{
    /**
     * @var AnnotationContainer アノテーションコンテナ
     */
    private $annotaion;

    /**
     * @var AnnotationContainer 注入結果
     */
    private $injectedContainer;

    /**
     * {@inheritdoc}
     */
    public function onInject(AnnotationContainer $annotation)
    {
        $this->annotation = $annotation;
        $this->injectedContainer = new AnnotationContainer();
        Logger::debug("@Filter injected.");
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
    public function onMethodInject(CoreInterface &$instance, Container $container, \ReflectionMethod $method)
    {
        $this->injectedContainer->annotation = $this->annotation;
        $this->injectedContainer->method = $method;
        $this->injectedContainer->classpath = get_class($instance);
        $this->injectedContainer->action = $container->router->action();
    }
}
