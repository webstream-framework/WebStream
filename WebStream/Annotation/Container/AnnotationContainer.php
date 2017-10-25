<?php
namespace WebStream\Annotation\Container;

use WebStream\Container\Container;

/**
 * AnnotationContainer
 * @author Ryuichi TANAKA.
 * @since 2014/05/19
 * @version 0.4
 */
class AnnotationContainer extends Container
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct(false);
    }
}
