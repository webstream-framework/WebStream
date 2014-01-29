<?php
namespace WebStream\Test\DataProvider;

/**
 * UtilityProvider
 * @author Ryuichi TANAKA.
 * @since 2013/09/21
 * @version 0.4
 */
trait UtilityProvider
{
    public function fileSearchIteratorProvider()
    {
        return [
            ["/core/WebStream/Delegate/Resolver.php"],
            ["/core/WebStream/Http/Request.php"]
        ];
    }

    public function readNamespaceProvider()
    {
        return [
            [
                '/Users/stay/workspace2/WebStream/core/WebStream/Test/TestData/UtilityReadNamespace.php',
                '\WebStream\Test\TestData'
            ]
        ];
    }

    public function readNoNamespaceProvider()
    {
        return [
            [
                '/Users/stay/workspace2/WebStream/core/WebStream/Test/TestData/UtilityReadNoNamespace.php'
            ]
        ];
    }
}
