<?php
namespace WebStream\Exception\Extend;

use WebStream\Exception\ApplicationException;

/**
 * MethodNotFoundException
 * @author Ryuichi TANAKA.
 * @since 2013/09/22
 * @version 0.4
 */
class MethodNotFoundException extends ApplicationException
{
    /**
     * constructor
     */
    public function __construct($message = null)
    {
        parent::__construct($message, 500, $this);
    }
}
