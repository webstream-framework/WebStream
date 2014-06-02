<?php
namespace WebStream\Annotation;

use WebStream\Module\Logger;

/**
 * TemplateCache
 * @author Ryuichi TANAKA.
 * @since 2013/10/30
 * @version 0.4
 *
 * @Annotation
 * @Target("METHOD")
 */
class TemplateCache extends Annotation
{
    /**
     * {@inheritdoc}
     */
    public function onInject()
    {
        Logger::debug("@TemplateCache injected.");
    }
}
