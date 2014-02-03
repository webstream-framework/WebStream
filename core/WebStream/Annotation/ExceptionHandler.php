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
    /** exceptionClasspathList */
    private $exceptionClasspathList;

    /**
     * ＠Override
     */
    public function onInject()
    {
        if (array_key_exists($this->EXCEPTIONHANDLER_ATTR_VALUE, $this->annotations)) {
            $this->exceptionClasspathList = $this->annotations[$this->EXCEPTIONHANDLER_ATTR_VALUE];
            if (!is_array($this->exceptionClasspathList)) {
                $this->exceptionClasspathList = [$this->exceptionClasspathList];
            }
        }
        Logger::debug("ExceptionHandler.");
    }

    /**
     * 例外クラスパスリストを返却する
     * @return array<string> 例外クラスパスリスト
     */
    public function getExceptionClasspathList()
    {
        return $this->exceptionClasspathList;
    }
}
