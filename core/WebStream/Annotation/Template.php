<?php
namespace WebStream\Annotation;

use WebStream\Module\Logger;

/**
 * Template
 * @author Ryuichi TANAKA.
 * @since 2013/10/10
 * @version 0.4.1
 *
 * @Annotation
 * @Target("METHOD")
 */
class Template extends Annotation
{
    /**
     * {@inheritdoc}
     */
    public function onInject()
    {
        Logger::debug("@Template injected.");
    }
}
