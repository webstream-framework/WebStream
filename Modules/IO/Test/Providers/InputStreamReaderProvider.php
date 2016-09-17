<?php
namespace WebStream\IO\Test\Providers;

use WebStream\IO\FileInputStream;
use WebStream\IO\StringInputStream;

/**
 * InputStreamReaderProvider
 * @author Ryuichi TANAKA.
 * @since 2016/08/18
 * @version 0.7
 */
trait InputStreamReaderProvider
{
    public function readCharProvider()
    {
        return [
            [new FileInputStream(dirname(__FILE__)  . "/../Fixtures/inputstreamreader-test1.txt"), "a", 1],
            [new FileInputStream(dirname(__FILE__)  . "/../Fixtures/inputstreamreader-test1.txt"), "a\n", 100], // over eof
            [new FileInputStream(dirname(__FILE__)  . "/../Fixtures/inputstreamreader-test2.txt"), "あ", 3],
            [new FileInputStream(dirname(__FILE__)  . "/../Fixtures/inputstreamreader-test2.txt"), "あ\n", 100], // over eof
            [new StringInputStream("a"), "a", 1],
            [new StringInputStream("a"), "a", 100], // over eof
            [new StringInputStream("あ"), "あ", 3],
            [new StringInputStream("あ"), "あ", 100] // over eof
        ];
    }

    public function readLineProvider()
    {
        return [
            [new FileInputStream(dirname(__FILE__)  . "/../Fixtures/inputstreamreader-test3.txt"), "test1", "test2"],
            [new StringInputStream("test1\ntest2\n"), "test1", "test2"]
        ];
    }

    public function closeProvider()
    {
        return [
            [new FileInputStream(dirname(__FILE__)  . "/../Fixtures/inputstreamreader-test1.txt")],
            [new StringInputStream("a\n")]
        ];
    }

    public function skipProvider()
    {
        return [
            [new FileInputStream(dirname(__FILE__)  . "/../Fixtures/inputstreamreader-test4.txt"), "a", 0],
            [new FileInputStream(dirname(__FILE__)  . "/../Fixtures/inputstreamreader-test4.txt"), "b", 1],
            [new FileInputStream(dirname(__FILE__)  . "/../Fixtures/inputstreamreader-test4.txt"), "d", 3],
            [new FileInputStream(dirname(__FILE__)  . "/../Fixtures/inputstreamreader-test4.txt"), "f", 5],
            [new FileInputStream(dirname(__FILE__)  . "/../Fixtures/inputstreamreader-test4.txt"), "\n", 9],
            [new FileInputStream(dirname(__FILE__)  . "/../Fixtures/inputstreamreader-test4.txt"), "", 10],
            [new StringInputStream("abcdefghi"), "a", 0],
            [new StringInputStream("abcdefghi"), "b", 1],
            [new StringInputStream("abcdefghi"), "d", 3],
            [new StringInputStream("abcdefghi"), "f", 5],
            [new StringInputStream("abcdefghi"), "", 9],
            [new StringInputStream("abcdefghi"), "", 10]
        ];
    }

    public function overSkipAndReadProvider()
    {
        return [
            [new FileInputStream(dirname(__FILE__)  . "/../Fixtures/inputstreamreader-test5.txt"), 4],
            [new FileInputStream(dirname(__FILE__)  . "/../Fixtures/inputstreamreader-test6.txt"), 100],
            [new StringInputStream("abc\n"), 4],
            [new StringInputStream("abcde\nあいうえお"), 100]
        ];
    }

    public function frontSkipProvider()
    {
        return [
            [new FileInputStream(dirname(__FILE__)  . "/../Fixtures/inputstreamreader-test8.txt"), 3, -1, "c"],
            [new StringInputStream("abcde\n"), 3, -1, "c"]
        ];
    }

    public function overFrontSkipProvider()
    {
        return [
            [new FileInputStream(dirname(__FILE__)  . "/../Fixtures/inputstreamreader-test1.txt"), -1],
            [new FileInputStream(dirname(__FILE__)  . "/../Fixtures/inputstreamreader-test1.txt"), -100],
            [new StringInputStream("a\n"), -1],
            [new StringInputStream("a\n"), -100]
        ];
    }

    public function resetProvider()
    {
        return [
            [new FileInputStream(dirname(__FILE__)  . "/../Fixtures/inputstreamreader-test1.txt"), 1, "a"],
            [new StringInputStream("a\n"), 1, "a"]
        ];
    }

    public function markAndResetProvider()
    {
        return [
            [new FileInputStream(dirname(__FILE__)  . "/../Fixtures/inputstreamreader-test7.txt"), 3, "d"],
            [new StringInputStream("abcde\n"), 3, "d"]
        ];
    }

    public function invalidLengthProvider()
    {
        return [
            [new FileInputStream(dirname(__FILE__)  . "/../Fixtures/inputstreamreader-test1.txt"), "a"],
            [new StringInputStream("a\n"), "a"]
        ];
    }
}
