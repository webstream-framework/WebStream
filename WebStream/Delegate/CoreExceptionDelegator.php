<?php
namespace WebStream\Delegate;

use WebStream\Exception\Extend\ClassNotFoundException;

/**
 * CoreExceptionDelegator
 * @author Ryuichi TANAKA.
 * @since 2014/05/05
 * @version 0.4
 */
class CoreExceptionDelegator
{
    /**
     * @var stirng 例外クラスパス
     */
    private $classpath;

    /**
     * @var stirng 例外メッセージ
     */
    private $message;

    /**
     * constructor
     * @param stirng 例外クラスパス
     * @param string 例外メッセージ
     */
    public function __construct($classpath, $message)
    {
        $this->classpath = $classpath;
        $this->message = $message;
    }

    /**
     * method missing
     */
    public function __call($method, $arguments)
    {
        if (class_exists($this->classpath)) {
            throw new $this->classpath($this->message);
        } else {
            throw new ClassNotFoundException($this->classpath . " is not found.");
        }
    }
}
