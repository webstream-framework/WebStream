<?php
namespace WebStream\Exception\Extend;

use WebStream\Exception\ApplicationException;

/**
 * DatabaseException
 * @author Ryuichi TANAKA.
 * @since 2013/12/10
 * @version 0.4
 */
class DatabaseException extends ApplicationException
{
    /**
     * constructor
     */
    public function __construct($message = null)
    {
        parent::__construct($message, 500, $this);
    }
}
