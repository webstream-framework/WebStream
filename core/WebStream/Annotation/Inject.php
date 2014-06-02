<?php
namespace WebStream\Annotation;

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
    public function onInject()
    {
        Logger::debug("@Injected found.");
    }
}
