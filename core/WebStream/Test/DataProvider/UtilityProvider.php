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
                '/core/WebStream/Test/TestData/UtilityReadNamespace.php',
                '\WebStream\Test\TestData'
            ]
        ];
    }

    public function readNoNamespaceProvider()
    {
        return [
            [
                '/core/WebStream/Test/TestData/UtilityReadNoNamespace.php'
            ]
        ];
    }

    public function customInArrayProvider()
    {
        return [
            ["b", ["a","b","c"]],
            [3, [1,2,3,4]],
            [1.3, [1.2,1.3,1.4,1.5]]
        ];
    }
}
