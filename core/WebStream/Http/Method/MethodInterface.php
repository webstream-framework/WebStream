<?php
namespace WebStream\Http\Method;

/**
 * MethodInterface
 * @author Ryuichi TANAKA.
 * @since 2013/09/19
 * @version 0.4
 */
interface MethodInterface
{
    /**
     * request parameter
     * @return array<string> parameter
     */
    public function params();
}
