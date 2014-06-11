<?php
namespace WebStream\Annotation;

use WebStream\Module\Logger;

/**
 * Database
 * @author Ryuichi TANAKA.
 * @since 2013/12/07
 * @version 0.4
 *
 * @Annotation
 * @Target("CLASS")
 */
class Database extends Annotation
{
    /**
     * {@inheritdoc}
     */
    public function onInject()
    {
        Logger::debug("@Database injected.");
    }
}
