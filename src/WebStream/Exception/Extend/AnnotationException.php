<?php
namespace WebStream\Exception\Extend;

use WebStream\Exception\ApplicationException;

/**
 * AnnotationException
 * @author Ryuichi TANAKA.
 * @since 2013/09/18
 * @version 0.4
 */
class AnnotationException extends ApplicationException
{
    /**
     * constructor
     */
    public function __construct($message = null)
    {
        parent::__construct($message, 500, $this);
    }
}
