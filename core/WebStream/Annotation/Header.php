<?php
namespace WebStream\Annotation;

use WebStream\Module\Logger;

/**
 * Header
 * @author Ryuichi TANAKA.
 * @since 2013/10/20
 * @version 0.4
 *
 * @Annotation
 * @Target("METHOD")
 */
class Header extends Annotation
{
    /**
     * {@inheritdoc}
     */
    public function onInject()
    {
        Logger::debug("@Header injected.");
    }
}
