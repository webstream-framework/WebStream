<?php
namespace WebStream\Annotation;

use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Module\Logger;

/**
 * Inject
 * @author Ryuichi TANAKA.
 * @since 2013/09/11
 * @version 0.4
 *
 * @Annotation
 * @Target({"CLASS","METHOD","PROPERTY"})
 */
class Inject extends Annotation
{
    /**
     * {@inheritdoc}
     */
    public function onInject(AnnotationContainer $ignore)
    {
        Logger::debug("@Injected found.");
    }
}
