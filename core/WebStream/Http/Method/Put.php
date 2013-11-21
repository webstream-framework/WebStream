<?php
namespace WebStream\Http\Method;

use WebStream\Module\Security;

/**
 * Put
 * @author Ryuichi TANAKA.
 * @since 2013/11/21
 * @version 0.4
 */
class Put implements MethodInterface
{
    /**
     * @Override
     */
    public function params()
    {
        parse_str(file_get_contents('php://input'), $putdata);
        return Security::safetyIn($putdata);
    }
}
