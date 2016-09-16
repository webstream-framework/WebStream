<?php
namespace WebStream\IO\Test\Providers;

/**
 * FileReaderProvider
 * @author Ryuichi TANAKA.
 * @since 2016/08/18
 * @version 0.7
 */
trait FileReaderProvider
{
    public function readProvider()
    {
        return [
            [dirname(__FILE__)  . "/../Fixtures/filereader-test1.txt", "test1\n" . "test2\n"]
        ];
    }
}
