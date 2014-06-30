<?php
namespace WebStream\Delegate;

/**
 * ExceptionDelegator
 * @author Ryuichi TANAKA.
 * @since 2014/05/05
 * @version 0.4
 */
class ExceptionDelegator
{
    /**
     * @var \Exception 例外オブジェクト
     */
    private $exception;

    /**
     * constructor
     * @param \Exception 例外オブジェクト
     */
    public function __construct(\Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->exception = null;
    }

    /**
     * method missing
     */
    public function __call($method, $arguments)
    {
        if ($this->exception instanceof \Exception) {
            throw $this->exception;
        }
    }
}
