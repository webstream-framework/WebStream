<?php
namespace WebStream\Exception\Delegate;

use WebStream\DI\Injector;
use WebStream\Exception\SystemException;
use WebStream\Exception\DelegateException;

/**
 * ExceptionDelegator
 * @author Ryuichi TANAKA.
 * @since 2014/05/05
 * @version 0.7
 */
class ExceptionDelegator
{
    use Injector;

    /**
     * @var Logger ロガー
     */
    private $logger;

    /**
     * @var object インスタンス
     */
    private $instance;

    /**
     * @var string メソッド名
     */
    private $method;

    /**
     * @var \Exception 例外オブジェクト
     */
    private $exceptionObject;

    /**
     * @var array<Container> 例外ハンドリングリスト
     */
    private $exceptionHandler;

    /**
     * constructor
     */
    public function __construct($instance, \Exception $exceptionObject, string $method = null)
    {
        $this->instance = $instance;
        $this->method = $method;
        $this->exceptionObject = $exceptionObject;
        $this->exceptionHandler = [];
        $this->logger = new class() { function __call($name, $args) {} };
    }

    /**
     * 例外ハンドリングリストを設定する
     * @param array<Container> 例外ハンドリングリスト
     */
    public function setExceptionHandler(array $exceptionHandler)
    {
        $this->exceptionHandler = $exceptionHandler;
    }

    /**
     * 例外を実行する
     */
    public function raise()
    {
        $originException = $this->exceptionObject;
        $delegateException = null;
        if ($originException instanceof DelegateException) {
            // 複数レイヤ間で例外がやりとりされる場合、すでにDelegateExceptionでラップ済みなので戻す
            $originException = $originException->getOriginException();
        }
        $invokeMethods = [];
        foreach ($this->exceptionHandler as $exceptionHandlerAnnotation) {
            $exceptions = $exceptionHandlerAnnotation['exceptions'];
            $refMethod = $exceptionHandlerAnnotation['refMethod'];
            foreach ($exceptions as $exception) {
                if (is_a($originException, is_object($exception) ? get_class($exception) : $exception)) {
                    // 一つのメソッドに複数の捕捉例外が指定された場合(派生例外クラス含む)、先勝で1回のみ実行する
                    // そうでなければ複数回メソッドが実行されるため
                    // ただし同一クラス内に限る(親クラスの同一名のメソッドは実行する)
                    $classpath = $refMethod->class . "#" . $refMethod->name;
                    if (!array_key_exists($classpath, $invokeMethods)) {
                        $invokeMethods[$classpath] = $refMethod;
                    }
                }
            }
        }
        if (count($invokeMethods) > 0) {
            $delegateException = new DelegateException($this->exceptionObject);
            $delegateException->enableHandled();
        }
        foreach ($invokeMethods as $classpath => $invokeMethod) {
            $params = ["class" => get_class($this->instance), "method" => $this->method, "exception" => $originException];
            $invokeMethod->invokeArgs($this->instance, [$params]);
            $this->logger->debug("Execution of handling is success: " . $classpath);
        }
        throw $delegateException ?: $originException;
    }
}
