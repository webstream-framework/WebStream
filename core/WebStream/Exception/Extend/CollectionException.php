<?php
namespace WebStream\Exception\Extend;

use WebStream\Exception\UncatchableException;

/**
 * CollectionException
 * @author Ryuichi TANAKA.
 * @since 2013/12/14
 * @version 0.4
 */
class CollectionException extends UncatchableException
{
    /**
     * constructor
     */
    public function __construct($message = null)
    {
        parent::__construct($message, 500, $this);
    }
}
