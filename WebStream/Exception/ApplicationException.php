<?php
namespace WebStream\Exception;

use WebStream\Log\Logger;
use WebStream\Log\LoggerUtils;

/**
 * ApplicationException
 * @author Ryuichi TANAKA.
 * @since 2014/05/05
 * @version 0.4
 */
class ApplicationException extends \LogicException
{
    use LoggerUtils;

    /**
     * constructor
     */
    public function __construct($message, $code = 500, $exception = null)
    {
        parent::__construct($message, $code);

        if (!Logger::isInitialized()) {
            return;
        }

        if ($exception === null) {
            $exception = $this;
        }

        Logger::error(get_class($exception) . " is thrown: " . $exception->getFile() . "(" . $exception->getLine() . ")");
        if (!empty($message)) {
            Logger::error($this->addStackTrace($this->getMessage(), $this->getTraceAsString()));
        }
    }
}
