<?php
namespace WebStream\DI;

use WebStream\Container\Container;

/**
 * Injector
 * @author Ryuichi TANAKA.
 * @since 2015/12/26
 * @version 0.7
 */
trait Injector
{
    /**
     * @var array<string> プロパティマップ
     */
    private $__propertyMap;

    /**
     * オブジェクトを注入する
     * @param string プロパティ名
     * @param mixed オブジェクト
     * @return Injector
     */
    public function inject($name, $object)
    {
        $this->{$name} = $object;

        return $this;
    }

    /**
     * @var Container プロパティコンテナ
     */
    private $__propertyContainer;

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
