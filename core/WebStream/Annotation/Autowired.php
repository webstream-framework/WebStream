<?php
namespace WebStream\Annotation;

use WebStream\Module\Logger;

/**
 * Autowired
 * @author Ryuichi TANAKA.
 * @since 2013/09/17
 * @version 0.4
 *
 * @Annotation
 * @Target("PROPERTY")
 */
class Autowired extends Annotation
{
    /**
     * {@inheritdoc}
     */
    public function onInject()
    {
        Logger::debug("@Autowired injected.");
    }
}
