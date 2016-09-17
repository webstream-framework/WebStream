<?php
namespace WebStream\DI;

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
     * overload setter
     */
    public function __set($name, $value)
    {
        if ($this->__propertyMap === null) {
            $this->__propertyMap = [];
        }
        $this->__propertyMap[$name] = $value;
    }

    /**
     * overload setter
     */
    public function __get($name)
    {
        return $this->__propertyMap !== null && array_key_exists($name, $this->__propertyMap) ? $this->__propertyMap[$name] : null;
    }

    /**
     * overload isset
     */
    public function __isset($name)
    {
        return $this->__propertyMap === null && array_key_exists($name, $this->__propertyMap) === false;
    }

    /**
     * overload unset
     */
    public function __unset($name)
    {
        if ($this->__propertyMap !== null && array_key_exists($name, $this->__propertyMap)) {
            unset($this->__propertyMap[$name]);
        }
    }

    /**
     * コンテナクリア
     */
    public function __clear()
    {
        $this->__propertyMap = null;
    }
}
