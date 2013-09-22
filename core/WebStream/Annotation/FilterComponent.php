<?php
namespace WebStream\Annotation;

use WebStream\Exception\MethodNotFoundException;

/**
 * FilterComponent
 * @author Ryuichi TANAKA.
 * @since 2013/09/19
 * @version 0.4
 */
class FilterComponent
{
    /** 実行対象のリフレクションクラス */
    private $refClass;

    /** refClassのインスタンス */
    private $instance;

    /** initialize filter method */
    private $initializeMethod;

    /** before filter queue */
    private $beforeQueue = [];

    /** after filter queue */
    private $afterQueue = [];

    /**
     * initialize filter methodに実行するメソッドを設定する
     * @param object メソッドオブジェクト
     */
    public function setInitializeMethod(\ReflectionMethod $method)
    {
        $this->initializeMethod = $method;
    }

    /**
     * before filter queueに実行するメソッドを設定する
     * @param object メソッドオブジェクト
     */
    public function setBeforeQueue(\ReflectionMethod $method)
    {
        $this->beforeQueue[] = $method;
    }

    /**
     * after filter queueに実行するメソッドを設定する
     * @param object メソッドオブジェクト
     */
    public function setAfterQueue(\ReflectionMethod $method)
    {
        $this->afterQueue[] = $method;
    }

    /**
     * 実行対象のインスタンスを設定する
     * @param object インスタンス
     */
    public function setInstance(\ReflectionClass $refClass)
    {
        $this->refClass = $refClass;
        $this->instance = $refClass->newInstanceWithoutConstructor();
    }

    /**
     * constructor
     */
    public function __construct($arguments)
    {
        $this->executeConstructor($arguments);
        $this->executeInitializeFilter();
        $this->executeBeforeFilter();
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->executeAfterFilter();
    }

    private function executeConstructor($arguments) {
        $constructor = $this->refClass->getConstructor();
        $constructor->invokeArgs($this->instance, $arguments);
    }

    /**
     * initialize filterを実行する
     */
    private function executeInitializeFilter()
    {
        if ($this->initializeMethod !== null) {
            $this->initializeMethod->invoke($this->instance);
        }
    }

    /**
     * before filterを実行する
     */
    private function executeBeforeFilter()
    {
        foreach ($this->beforeQueue as $method) {
            $method->invoke($this->instance);
        }
    }

    /**
     * 対象インスタンスのメソッドを実行する
     * @param string メソッド名
     * @param array 引数のリスト
     * @return mixed 戻り値
     */
    public function executeAction($methodName, $arguments = [])
    {
        try {
            $method = $this->refClass->getMethod($methodName);
            $instance = $this->refClass->newInstanceWithoutConstructor();

            return $method->invokeArgs($instance, [$arguments]);
        } catch (\ReflectionException $e) {
            $className = $this->refClass->getName();
            throw new MethodNotFoundException("Method not found at $className: $methodName");
        }
    }

    /**
     * after filterを実行する
     */
    private function executeAfterFilter()
    {
        foreach ($this->afterQueue as $method) {
            $method->invoke($this->instance);
        }
    }
}
