<?php
namespace WebStream\Exception;

use WebStream\Module\Logger;

/**
 * UncatchableException
 * @author Ryuichi TANAKA.
 * @since 2014/05/05
 * @version 0.4
 */
class UncatchableException extends \RuntimeException
{
    /**
     * constructor
     */
    public function __construct($message, $code = 500, $exception = null)
    {
        parent::__construct($message, $code);
        if ($exception === null) {
            $exception = $this;
        }
        Logger::error(get_class($exception) . " is thrown: " . $exception->getFile() . "(" . $exception->getLine() . ")");
        if (!empty($message)) {
            Logger::error($this->getMessage(), $this->getTraceAsString());
        }
    }
}
