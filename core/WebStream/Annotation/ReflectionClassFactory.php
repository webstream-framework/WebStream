<?php
namespace WebStream\Annotation;

use WebStream\Module\ClassLoader;

/**
 * ReflectionClassFactory
 * @author Ryuichi TANAKA.
 * @since 2013/09/17
 * @version 0.4
 */
abstract class ReflectionClassFactory
{
    /** クラスローダ */
    protected $classLoader;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->classLoader = new ClassLoader();
        $this->classLoad();
    }

    /**
     * インスタンスを返却する
     * @param string クラスパス
     * @return object インスタンス
     */
    public function create($classpath)
    {
        return $this->createReflectionClassInstance($classpath);
    }

    /**
     * インスタンスを返却する抽象メソッド
     * @param string クラスパス
     */
    abstract protected function createReflectionClassInstance($classpath);

    /**
     * クラスローダを実行する抽象メソッド
     */
    abstract protected function classLoad();
}
