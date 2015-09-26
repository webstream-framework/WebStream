<?php
namespace WebStream\Exception\Extend;

use WebStream\Exception\ApplicationException;

/**
 * ClassNotFoundException
 * @author Ryuichi TANAKA.
 * @since 2013/12/14
 * @version 0.4
 */
class ClassNotFoundException extends ApplicationException
{
    /**
     * constructor
     */
    public function __construct($message = null)
    {
        parent::__construct($message, 500, $this);
    }

    /**
     * method missing
     */
    public function __call($name, $arguments)
    {
        throw $this;
    }
}
