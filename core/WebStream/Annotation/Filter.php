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
    /** initialize filter */
    private $isInitialize;

    /** before filter */
    private $isBefore;

    /** after filter */
    private $isAfter;

    /**
     * @Override
     */
    public function onInject()
    {
        $this->isInitialize = in_array($this->FILTER_VALUE_INITIALIZE, $this->annotations);
        $this->isBefore = in_array($this->FILTER_VALUE_BEFORE, $this->annotations);
        $this->isAfter  = in_array($this->FILTER_VALUE_AFTER, $this->annotations);

        if ($this->isInitialize) {
            Logger::debug("Initialize filter enabled.");
        }
        if ($this->isBefore) {
            Logger::debug("Before filter enabled.");
        }
        if ($this->isAfter) {
            Logger::debug("After filter enabled.");
        }
    }

    /**
     * initialize filterを実行するかどうか
     * @return boolean 実行するかどうか
     */
    public function enableInitialize()
    {
        return $this->isInitialize;
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
