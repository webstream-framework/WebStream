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

    /** before filter info */
    private $beforeMap;

    /** after filter info */
    private $afterMap;

    /**
     * @Override
     */
    public function onInject()
    {

        $this->isInitialize = $this->annotations[$this->FILTER_ATTR_TYPE] === $this->FILTER_VALUE_INITIALIZE;

        if ($this->annotations[$this->FILTER_ATTR_TYPE] === $this->FILTER_VALUE_BEFORE) {
            Logger::debug("Before filter enabled.");
            $this->beforeMap = [];
            if (array_key_exists($this->FILTER_ATTR_EXCEPT, $this->annotations)) {
                if (is_array($this->annotations[$this->FILTER_ATTR_EXCEPT])) {
                    $this->beforeMap[$this->FILTER_ATTR_EXCEPT] = $this->annotations[$this->FILTER_ATTR_EXCEPT];
                } else {
                    $this->beforeMap[$this->FILTER_ATTR_EXCEPT] = [$this->annotations[$this->FILTER_ATTR_EXCEPT]];
                }
            }
            if (array_key_exists($this->FILTER_ATTR_ONLY, $this->annotations)) {
                if (is_array($this->annotations[$this->FILTER_ATTR_ONLY])) {
                    $this->beforeMap[$this->FILTER_ATTR_ONLY] = $this->annotations[$this->FILTER_ATTR_ONLY];
                } else {
                    $this->beforeMap[$this->FILTER_ATTR_ONLY] = [$this->annotations[$this->FILTER_ATTR_ONLY]];
                }
            }
        }

        if ($this->annotations[$this->FILTER_ATTR_TYPE] === $this->FILTER_VALUE_AFTER) {
            Logger::debug("After filter enabled.");
            $this->afterMap = [];
            if (array_key_exists($this->FILTER_ATTR_EXCEPT, $this->annotations)) {
                if (is_array($this->annotations[$this->FILTER_ATTR_EXCEPT])) {
                    $this->afterMap[$this->FILTER_ATTR_EXCEPT] = $this->annotations[$this->FILTER_ATTR_EXCEPT];
                } else {
                    $this->afterMap[$this->FILTER_ATTR_EXCEPT] = [$this->annotations[$this->FILTER_ATTR_EXCEPT]];
                }
            }
            if (array_key_exists($this->FILTER_ATTR_ONLY, $this->annotations)) {
                if (is_array($this->annotations[$this->FILTER_ATTR_ONLY])) {
                    $this->afterMap[$this->FILTER_ATTR_ONLY] = $this->annotations[$this->FILTER_ATTR_ONLY];
                } else {
                    $this->afterMap[$this->FILTER_ATTR_ONLY] = [$this->annotations[$this->FILTER_ATTR_ONLY]];
                }
            }
        }

        if ($this->isInitialize) {
            Logger::debug("Initialize filter enabled.");
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
     * before filterの情報を返却する
     * @return array<string> before filter情報
     */
    public function getBeforeInfo()
    {
        return $this->beforeMap;
    }

    /**
     * after filterの情報を返却する
     * @return array<string> after filter情報
     */
    public function getAfterInfo()
    {
        return $this->afterMap;
    }
}
