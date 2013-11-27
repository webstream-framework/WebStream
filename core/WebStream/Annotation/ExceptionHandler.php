<?php
namespace WebStream\Annotation;

use WebStream\Module\Logger;

/**
 * ExceptionHandler
 * @author Ryuichi TANAKA.
 * @since 2013/11/22
 * @version 0.4
 *
 * @Annotation
 * @Target("METHOD")
 */
class ExceptionHandler extends AbstractAnnotation
{
    /** exceptionClasspath */
    private $exceptionClasspath;

    /**
     * ＠Override
     */
    public function onInject()
    {
        if (array_key_exists($this->EXCEPTIONHANDLER_ATTR_VALUE, $this->annotations)) {
            $this->exceptionClasspath = $this->annotations[$this->EXCEPTIONHANDLER_ATTR_VALUE];
        }
        Logger::debug("ExceptionHandler.");
    }

    /**
     * 例外クラスパスを返却する
     * @return string 例外クラスパス
     */
    public function getExceptionClasspath()
    {
        return $this->exceptionClasspath;
    }
}
