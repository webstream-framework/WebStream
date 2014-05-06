<?php
namespace WebStream\Exception\Extend;

/**
 * LoggerException
 * @author Ryuichi TANAKA.
 * @since 2013/09/08
 * @version 0.4
 */
class LoggerException extends \RuntimeException
{
    /**
     * constructor
     */
    public function __construct($message = null)
    {
        parent::__construct($message, 500);
    }
}
