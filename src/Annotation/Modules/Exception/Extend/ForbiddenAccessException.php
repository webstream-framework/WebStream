<?php
namespace WebStream\Exception\Extend;

use WebStream\Exception\ApplicationException;

/**
 * ForbiddenAccessException
 * @author Ryuichi TANAKA.
 * @since 2013/11/26
 * @version 0.4
 */
class ForbiddenAccessException extends ApplicationException
{
    /**
     * constructor
     */
    public function __construct($message = null)
    {
        parent::__construct($message, 403, $this);
    }
}
