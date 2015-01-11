<?php
namespace WebStream\Core;

use WebStream\Module\Container;

/**
 * CoreInterface
 * @author Ryuichi TANAKA.
 * @since 2013/12/09
 * @version 0.4
 */
interface CoreInterface
{
    /**
     * Constructor
     * @param object DIContainer
     */
    public function __construct(Container $container);

    /**
     * Destructor
     */
    public function __destruct();
}
