<?php
namespace WebStream\Annotation;

use WebStream\Core\CoreController;
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

    /** Controllerインスタンス */
    protected $instance;

    /**
     * コンストラクタ
     * @param object Controllerインスタンス
     */
    public function __construct(CoreController $instance = null)
    {
        $this->instance = $instance;
    }

    /**
     * アノテーションを実行する
     * @param object リフレクションクラスオブジェクト
     * @param string 実行対象のメソッド名(指定無しの場合全てのメソッドが対象)
     * @param array コンストラクタ引数のリスト
     * @return object インスタンス
     */
    public function read(\ReflectionClass $refClass, $method = null, $arguments = [])
    {
        if ($this->instance === null) {
            $this->instance = $refClass->newInstanceWithoutConstructor();
        }

        return $this->readAnnotation($refClass, $method, $arguments);
    }

    /**
     * インスタンスを返却する
     * @return object Controllerインスタンス
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * インスタンスを返却する抽象メソッド
     * @param string クラスパス
     * @param array 引数リスト
     */
    abstract protected function readAnnotation($refClass, $method, $arguments);
}
