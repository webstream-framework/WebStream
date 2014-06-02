<?php
namespace WebStream\Annotation;

use WebStream\Module\Logger;

/**
 * Filter
 * @author Ryuichi TANAKA.
 * @since 2013/09/11
 * @version 0.4
 *
 * @Annotation
 * @Target("METHOD")
 */
class Filter extends Annotation
{
    /**
     * {@inheritdoc}
     */
    public function onInject()
    {
        Logger::debug("@Filter injected.");
    }
}
