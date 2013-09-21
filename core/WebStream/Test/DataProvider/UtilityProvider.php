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
    public function fileSearchProvider()
    {
        return [
            [
                'UtilityFileSearch1',
                '/Users/stay/workspace2/WebStream/core/WebStream/Test/TestData/UtilityFileSearch1.php'
            ]
        ];
    }

    public function multipleFileSearchProvider()
    {
        return [
            [
                'UtilityFileSearch',
                '/Users/stay/workspace2/WebStream/core/WebStream/Test/TestData/UtilityFileSearch1.php',
                '/Users/stay/workspace2/WebStream/core/WebStream/Test/TestData/UtilityFileSearch2.php'
            ]
        ];
    }

    public function regexpFileSearchProvider()
    {
        return [
            [
                '/UtilityFileSearch1\.php/',
                '/Users/stay/workspace2/WebStream/core/WebStream/Test/TestData/UtilityFileSearch1.php'
            ]
        ];
    }

    public function regexpMultipleFileSearchProvider()
    {
        return [
            [
                '/UtilityFileSearch/',
                '/Users/stay/workspace2/WebStream/core/WebStream/Test/TestData/UtilityFileSearch1.php',
                '/Users/stay/workspace2/WebStream/core/WebStream/Test/TestData/UtilityFileSearch2.php'
            ]
        ];
    }
}
