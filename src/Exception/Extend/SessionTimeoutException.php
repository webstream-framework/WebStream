<?php
namespace WebStream\Exception\Extend;

use WebStream\Exception\ApplicationException;

/**
 * SessionTimeoutException
 * @author Ryuichi TANAKA.
 * @since 2013/11/26
 * @version 0.4
 */
class SessionTimeoutException extends ApplicationException
{
    /**
     * constructor
     */
    public function __construct($message = null)
    {
        parent::__construct($message, 404, $this);
    }
}
