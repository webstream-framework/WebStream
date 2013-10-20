<?php
namespace WebStream\Annotation;

/**
 * Value
 * @author Ryuichi TANAKA.
 * @since 2013/09/17
 * @version 0.4
 *
 * @Annotation
 * @Target("PROPERTY")
 */

class Value extends AbstractAnnotation
{
    /** 値 */
    private $value;

    /**
     * @Override
     */
    public function onInject()
    {
        if (array_key_exists($this->VALUE_ATTR_VALUE, $this->annotations)) {
            $this->value = $this->annotations[$this->VALUE_ATTR_VALUE];
        }
    }

    /**
     * 値を返却する
     * @return object インスタンス
     */
    public function getValue()
    {
        return $this->value;
    }
}
