<?php
namespace WebStream\Annotation;

use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Base\IMethods;
use WebStream\Annotation\Base\IRead;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Container\Container;
use WebStream\Exception\Extend\AnnotationException;

/**
 * Alias
 * @author Ryuichi TANAKA.
 * @since 2015/12/05
 * @version 0.7
 *
 * @Annotation
 * @Target("METHOD")
 */
class Alias extends Annotation implements IMethods, IRead
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

        $aliasMethodName = $this->annotation->name;
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]{0,}$/', $aliasMethodName)) {
            throw new AnnotationException("Alias method is invalid: " . $aliasMethodName);
        }

        $this->injectedContainer->{$aliasMethodName} = $method->name;
    }
}
