<?php
namespace WebStream\Test\DataProvider;

/**
 * TemplateCacheProvider
 * @author Ryuichi TANAKA.
 * @since 2013/11/10
 * @version 0.4
 */
trait TemplateCacheProvider
{
    public function invalidExpireProvider()
    {
        return [
            ["error1"],
            ["error2"]
        ];
    }
}
