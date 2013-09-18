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
    /**  */
    protected $annotations;

    /**
     * Constructor
     */
    public function __construct($annotations = array())
    {
        $this->annotations = $annotations;
        $this->onInject();
    }

    /**
     * Injected event
     */
    abstract public function onInject();
}
