<?php
namespace WebStream\Exception\Extend;

use WebStream\Exception\SystemException;

/**
 * LoggerException
 * @author Ryuichi TANAKA.
 * @since 2013/09/08
 * @version 0.4
 */
class LoggerException extends SystemException
{
    /**
     * constructor
     */
    public function __construct($message = null)
    {
        parent::__construct($message, 500);
    }
}
