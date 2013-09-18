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
    /** 型名 */
    private $type;
    /** インスタンス */
    private $instance;

    /**
     * @Override
     */
    public function onInject()
    {
        if (array_key_exists("value", $this->annotations)) {
            $this->type = $this->annotations["value"];
            if (!class_exists($this->type)) {
                throw new AnnotationException("Undefined class found in @Autowired: " . $this->type);
            }
            $refClass = new \ReflectionClass($this->type);
            $this->instance = $refClass->newInstance();
        }
    }

    /**
     * 型名を返却する
     * @return string 型名
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * インスタンスを返却する
     * @return object インスタンス
     */
    public function getInstance()
    {
        return $this->instance;
    }
}
