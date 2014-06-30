<?php
namespace WebStream\Annotation;

use WebStream\Module\Logger;

/**
 * ExceptionHandler
 * @author Ryuichi TANAKA.
 * @since 2013/11/22
 * @version 0.4
 *
 * @Annotation
 * @Target("METHOD")
 */
class ExceptionHandler extends Annotation
{
    /**
     * {@inheritdoc}
     */
    public function onInject()
    {
        Logger::debug("@ExceptionHandler injected.");
    }
}
