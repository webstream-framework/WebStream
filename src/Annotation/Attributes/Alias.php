<?php
namespace WebStream\Annotation\Attributes;

use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Base\IMethods;
use WebStream\Annotation\Base\IRead;
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
     * @var array<string> 注入アノテーション情報
     */
    private $injectAnnotation;

    /**
     * @var array<string> 読み込みアノテーション情報
     */
    private $readAnnotation;

    /**
     * {@inheritdoc}
     */
    public function onInject(array $injectAnnotation)
    {
        $this->injectAnnotation = $injectAnnotation;
        $this->readAnnotation = ['method' => ''];
    }

    /**
     * {@inheritdoc}
     */
    public function getAnnotationInfo(): array
    {
        return $this->readAnnotation;
    }

    /**
     * {@inheritdoc}
     */
    public function onMethodInject(IAnnotatable $instance, \ReflectionMethod $method, Container $container)
    {
        $definedMethods = [];
        $refClass = new \ReflectionClass($instance);
        foreach ($refClass->getMethods() as $refMethod) {
            $definedMethods[] = $refMethod->name;
        }

        $aliasMethodName = $this->injectAnnotation['name'];
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]{0,}$/', $aliasMethodName)) {
            throw new AnnotationException("Alias method is invalid: " . $aliasMethodName);
        }

        if (array_key_exists($aliasMethodName, array_flip($definedMethods))) {
            throw new AnnotationException("Alias method of the same name is defined: $aliasMethodName");
        }

        if ($container->action === $aliasMethodName) {
            $this->readAnnotation['method'] = $method->name;
        }
    }
}
