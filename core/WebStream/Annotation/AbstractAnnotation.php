<?php
namespace WebStream\Annotation;

/**
 * AbstractAnnotation
 * @author Ryuichi TANAKA.
 * @since 2013/09/12
 * @version 0.4
 */
abstract class AbstractAnnotation
{
    use AnnotationConstants;

    /** annotation */
    protected $annotations;

    /**
     * Constructor
     */
    public function __construct($annotations = [])
    {
        $this->annotations = $annotations;
        $this->onInject();
    }

    /**
     * Injected event
     */
    abstract public function onInject();
}
