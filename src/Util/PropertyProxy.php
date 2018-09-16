<?php
namespace WebStream\Util;

use WebStream\Container\Container;

/**
 * PropertyProxy
 * @author Ryuichi Tanaka
 * @since 2015/05/02
 * @version 0.7
 */
trait PropertyProxy
{
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
