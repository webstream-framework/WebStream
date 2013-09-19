<?php
namespace WebStream\Http\Method;

use WebStream\Module\Security;

/**
 * Get
 * @author Ryuichi TANAKA.
 * @since 2013/09/19
 * @version 0.4
 */
class Get implements MethodInterface
{
    /**
     * @Override
     */
    public function params()
    {
        return Security::safetyIn($_GET);
    }
}
