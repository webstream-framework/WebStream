<?php
namespace WebStream\Exception\Extend;

use WebStream\Exception\SystemException;

/**
 * CollectionException
 * @author Ryuichi TANAKA.
 * @since 2013/12/14
 * @version 0.4
 */
class CollectionException extends SystemException
{
    /**
     * constructor
     */
    public function __construct($message = null)
    {
        parent::__construct($message, 500, $this);
    }
}
