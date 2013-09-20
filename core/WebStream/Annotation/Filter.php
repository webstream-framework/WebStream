<?php
namespace WebStream\Annotation;

use WebStream\Module\Logger;

/**
 * Filter
 * @author Ryuichi TANAKA.
 * @since 2013/09/11
 * @version 0.4
 *
 * @Annotation
 * @Target("METHOD")
 */
class Filter extends AbstractAnnotation
{
    /** before filter */
    private $isBefore;

    /** after filter */
    private $isAfter;

    /**
     * @Override
     */
    public function onInject()
    {
        $this->isBefore = in_array("Before", $this->annotations);
        $this->isAfter  = in_array("After", $this->annotations);
        if ($this->isBefore) {
            Logger::debug("Before filter enabled.");
        }
        if ($this->isAfter) {
            Logger::debug("After filter enabled.");
        }
    }

    /**
     * before filterを実行するかどうか
     * @return boolean 実行するかどうか
     */
    public function enableBefore()
    {
        return $this->isBefore;
    }

    /**
     * after filterを実行するかどうか
     * @return boolean 実行するかどうか
     */
    public function enableAfter()
    {
        return $this->isAfter;
    }
}
