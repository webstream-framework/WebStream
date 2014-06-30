<?php
namespace WebStream\Annotation;

use WebStream\Module\Logger;

/**
 * Query
 * @author Ryuichi TANAKA.
 * @since 2013/12/28
 * @version 0.4
 *
 * @Annotation
 * @Target("METHOD")
 */
class Query extends Annotation
{
    /**
     * {@inheritdoc}
     */
    public function onInject()
    {
        Logger::debug("@Query injected.");
    }
}
