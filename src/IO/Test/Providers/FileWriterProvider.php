<?php
namespace WebStream\IO\Test\Providers;

/**
 * FileWriterProvider
 * @author Ryuichi TANAKA.
 * @since 2016/08/18
 * @version 0.7
 */
trait FileWriterProvider
{
    public function writeProvider()
    {
        return [
            ["/tmp/filewriter-test1.txt", "test"]
        ];
    }

    public function writeAppendProvider()
    {
        return [
            ["/tmp/filewriter-test2.txt", "test", "testtest"]
        ];
    }
}
