<?php
namespace WebStream\DI;

use WebStream\Container\Container;
use WebStream\Exception\Extend\AnnotationException;
use PhpDocReader\PhpDocReader;

/**
 * Injector
 * @author Ryuichi TANAKA.
 * @since 2015/12/26
 * @version 0.7
 */
trait Injector
{
    /**
     * @var Container プロパティコンテナ
     */
    private $__propertyContainer;

    /**
     * オブジェクトを注入する
     * @param string プロパティ名
     * @param mixed オブジェクト
     * @return Injector
     */
    public function inject(string $name, $object)
    {
        $this->{$name} = $object;

        return $this;
    }

    /**
     * 型指定されたオブジェクトを注入する
     * @param string プロパティ名
     * @param mixed オブジェクト
     * @return Injector
     */
    public function strictInject(string $name, $object)
    {
        $reader = new PhpDocReader();
        try {
            $refClass = new \ReflectionClass($this);
            while ($refClass !== false) {
                if ($refClass->hasProperty($name)) {
                    $refProperty = $refClass->getProperty($name);
                    $classpath = $reader->getPropertyClass($refProperty);
                    if ($object instanceof $classpath) {
                        $this->inject($name, $object);
                    } else {
                        throw new AnnotationException("The type of injected property must be instance of ${classpath}");
                    }
                }
                $refClass = $refClass->getParentClass();
            }
        } catch (\ReflectionException $e) {
            throw new AnnotationException($e);
        }

        return $this;
    }

    /**
     * overload setter
     */
    public function __set($name, $value)
    {
        if ($this->__propertyContainer === null) {
            $this->__propertyContainer = new Container(false);
        }
        $this->__propertyContainer->{$name} = $value;
    }

    /**
     * overload setter
     */
    public function __get($name)
    {
        return $this->__propertyContainer !== null ? $this->__propertyContainer->{$name} : null;
    }

    /**
     * overload isset
     */
    public function __isset($name)
    {
        return $__propertyContainer === null || $__propertyContainer->{$name} === null;
    }

    /**
     * overload unset
     */
    public function __unset($name)
    {
        $this->__propertyContainer->remove($name);
    }

    /**
     * コンテナクリア
     */
    public function __clear()
    {
        $this->__propertyContainer = null;
    }
}
