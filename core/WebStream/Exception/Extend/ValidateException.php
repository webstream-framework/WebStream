<?php
namespace WebStream\Exception\Extend;

use WebStream\Exception\ApplicationException;

/**
 * ValidateException
 * @author Ryuichi TANAKA.
 * @since 2013/11/16
 * @version 0.4
 */
class ValidateException extends ApplicationException
{
    /**
     * constructor
     */
    public function __construct($message = null)
    {
        parent::__construct($message, 422, $this);
    }
}
