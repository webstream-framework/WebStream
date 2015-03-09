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
     * @param Container DIContainer
     */
    public function __construct(Container $container);

    /**
     * Destructor
     */
    public function __destruct();

    /**
     * 初期処理
     * @param Container DIContainer
     */
    public function __initialize(Container $container);
}
