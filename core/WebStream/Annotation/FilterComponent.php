<?php
namespace WebStream\Annotation;

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

    /** before filter queue */
    public $beforeQueue = [];

    /** after filter queue */
    public $afterQueue = [];

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
    }

    /**
     * constructor
     */
    public function __construct()
    {
        $this->executeBeforeFilter();
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->executeAfterFilter();
    }

    /**
     * before filterを実行する
     */
    private function executeBeforeFilter()
    {
        foreach ($this->beforeQueue as $method) {
            $method->invoke($this->refClass->newInstance());
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
        $method = $this->refClass->getMethod($methodName);
        $instance = $this->refClass->newInstanceWithoutConstructor();
        return $method->invokeArgs($instance, $arguments);
    }

    /**
     * after filterを実行する
     */
    private function executeAfterFilter()
    {
        foreach ($this->afterQueue as $method) {
            $method->invoke($this->refClass->newInstance());
        }
    }
}
