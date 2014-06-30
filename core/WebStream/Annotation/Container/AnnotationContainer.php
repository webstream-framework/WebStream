<?php
namespace WebStream\Annotation\Container;

use WebStream\Module\Container;

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
    public function get($key)
    {
        return isset($this->values[$key]) ? parent::get($key) : null;
    }
}
