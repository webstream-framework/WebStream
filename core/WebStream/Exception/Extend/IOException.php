<?php
namespace WebStream\Exception\Extend;

use WebStream\Exception\ApplicationException;

/**
 * IOException
 * @author Ryuichi TANAKA.
 * @since 2013/10/14
 * @version 0.4
 */
class IOException extends ApplicationException
{
    /**
     * constructor
     */
    public function __construct($message = null)
    {
        parent::__construct($message, 500, $this);
    }
}
