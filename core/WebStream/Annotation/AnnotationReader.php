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
     * インスタンスを返却する
     * @param object リフレクションクラスオブジェクト
     * @param string 実行対象のメソッド名(指定無しの場合全てのメソッドが対象)
     * @param array コンストラクタ引数のリスト
     * @return object インスタンス
     */
    public function read(\ReflectionClass $refClass, $method = null, $arguments = [])
    {
        return $this->readAnnotation($refClass, $method, $arguments);
    }

    /**
     * インスタンスを返却する抽象メソッド
     * @param string クラスパス
     * @param array 引数リスト
     */
    abstract protected function readAnnotation($refClass, $method, $arguments);
}
