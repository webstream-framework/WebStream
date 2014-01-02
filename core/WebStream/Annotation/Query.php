<?php
namespace WebStream\Annotation;

use WebStream\Exception\AnnotationException;

/**
 * Query
 * @author Ryuichi TANAKA.
 * @since 2013/12/28
 * @version 0.4
 *
 * @Annotation
 * @Target("METHOD")
 */
class Query extends AbstractAnnotation
{
    /** 値 */
    private $value;

    /**
     * Override
     */
    public function onInject()
    {
        if (array_key_exists($this->QUERY_ATTR_FILE, $this->annotations)) {
            $this->value = $this->annotations[$this->QUERY_ATTR_FILE];
        }
    }

    /**
     * 値を返却する
     * @return object 値
     */
    public function getValue()
    {
        return $this->value;
    }
}
