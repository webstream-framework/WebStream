<?php
namespace WebStream\Annotation;

use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IClass;
use WebStream\Annotation\Base\IMethods;
use WebStream\Annotation\Base\IProperty;
use WebStream\Annotation\Base\IRead;
use WebStream\Core\CoreInterface;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Module\Container;
use WebStream\Module\Logger;
use WebStream\Exception\Extend\AnnotationException;

/**
 * Alias
 * @author Ryuichi TANAKA.
 * @since 2015/12/05
 * @version 0.7
 *
 * @Annotation
 * @Target({"CLASS","METHOD","PROPERTY"})
 */
class Alias extends Annotation implements IClass, IMethods, IProperty, IRead
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
        $this->injectedContainer->class = new AnnotationContainer();
        $this->injectedContainer->method = new AnnotationContainer();
        $this->injectedContainer->property = new AnnotationContainer();
        Logger::debug("@Alias injected.");
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
    public function onClassInject(CoreInterface &$instance, Container $container, \ReflectionClass $class)
    {
        // not inmplement
    }

    /**
     * {@inheritdoc}
     */
    public function onMethodInject(CoreInterface &$instance, Container $container, \ReflectionMethod $method)
    {
        $aliasMethodName = $this->annotation->name;
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]{0,}$/', $aliasMethodName)) {
            throw new AnnotationException("Alias method is invalid: " . $aliasMethodName);
        }

        $this->injectedContainer->method->{$aliasMethodName} = $method->name;
    }

    /**
     * {@inheritdoc}
     */
    public function onPropertyInject(CoreInterface &$instance, Container $container, \ReflectionProperty $property)
    {
        // not inmplement
    }
}
