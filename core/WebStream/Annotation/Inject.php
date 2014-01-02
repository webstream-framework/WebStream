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
 * @Target({"CLASS","METHOD"})
 */
class Inject extends AbstractAnnotation
{
    /**
     * ＠Override
     */
    public function onInject()
    {
        Logger::debug("Injected.");
    }
}
