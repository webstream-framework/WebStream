<?php
namespace WebStream\Annotation;

use WebStream\Module\ClassLoader;

/**
 * AnnotationReader
 * @author Ryuichi TANAKA.
 * @since 2013/10/06
 * @version 0.4
 */
abstract class AnnotationReader
{
    /** クラスローダ */
    protected $classLoader;

    /** ReflectionClass */
    protected $refClass;

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
     * @param array 引数リスト
     * @return object インスタンス
     */
    public function read($classpath, $method = null)
    {
        return $this->readAnnotation($classpath, $method);
    }

    /**
     * インスタンスを返却する抽象メソッド
     * @param string クラスパス
     * @param array 引数リスト
     */
    abstract protected function readAnnotation($classpath, $arguments);

    /**
     * クラスローダを実行する抽象メソッド
     */
    abstract protected function classLoad();
}
