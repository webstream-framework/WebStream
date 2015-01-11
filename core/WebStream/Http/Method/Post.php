<?php
namespace WebStream\Http\Method;

use WebStream\Module\Security;

/**
 * Post
 * @author Ryuichi TANAKA.
 * @since 2013/10/20
 * @version 0.4
 */
class Post implements MethodInterface
{
    /**
     * @Override
     */
    public function params()
    {
        return Security::safetyIn($_POST);
    }
}
