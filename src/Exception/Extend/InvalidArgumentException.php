<?php
namespace WebStream\Exception\Extend;

use WebStream\Exception\SystemException;

/**
 * InvalidArgumentException
 * @author Ryuichi TANAKA.
 * @since 2014/05/06
 * @version 0.4
 */
class InvalidArgumentException extends SystemException
{
    /**
     * constructor
     */
    public function __construct($message = null)
    {
        parent::__construct($message, 500, $this);
    }
}
