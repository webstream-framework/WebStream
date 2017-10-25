<?php
namespace WebStream\Exception\Extend;

use WebStream\Exception\ApplicationException;

/**
 * RouterException
 * @author Ryuichi TANAKA.
 * @since 2013/09/08
 * @version 0.4
 */
class RouterException extends ApplicationException
{
    /**
     * constructor
     */
    public function __construct($message = null)
    {
        parent::__construct($message, 500, $this);
    }
}
