<?php
namespace WebStream\Annotation;

use WebStream\Exception\AnnotationException;

/**
 * Type
 * @author Ryuichi TANAKA.
 * @since 2013/09/17
 * @version 0.4
 *
 * @Annotation
 * @Target("PROPERTY")
 */
class Type extends AbstractAnnotation
{
    /** 値 */
    private $value;

    /**
     * @Override
     */
    public function onInject()
    {
        if (array_key_exists($this->TYPE_ATTR_VALUE, $this->annotations)) {
            $type = $this->annotations[$this->TYPE_ATTR_VALUE];
            if (!class_exists($type)) {
                throw new AnnotationException("Undefined class found in @Autowired: " . $type);
            }
            $refClass = new \ReflectionClass($type);
            $this->value = $refClass->newInstance();
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
