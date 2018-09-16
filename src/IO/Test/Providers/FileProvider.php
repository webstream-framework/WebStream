<?php
namespace WebStream\IO\Test\Providers;

use WebStream\IO\File;

/**
 * FileProvider
 * @author Ryuichi TANAKA.
 * @since 2016/08/19
 * @version 0.7
 */
trait FileProvider
{
    public function fileProvider()
    {
        return [
            [new File(dirname(__FILE__)  . "/../Fixtures/file-test1.txt")]
        ];
    }

    public function directoryProvider()
    {
        return [
            [new File(dirname(__FILE__)  . "/../Fixtures/file-test/")]
        ];
    }

    public function fileLinkProvider()
    {
        return [
            [new File(dirname(__FILE__)  . "/../Fixtures/file-test1.txt"), "/tmp/file-test-link"]
        ];
    }

    public function tmpFileProvider()
    {
        return [
            [new File("/tmp/file-test-tmp.txt")]
        ];
    }

    public function tmpDirectoryProvider()
    {
        return [
            [new File("/tmp/file-test-tmp/")]
        ];
    }

    public function renameFailureProvider()
    {
        return [
            [new File(dirname(__FILE__)  . "/../Fixtures/file-test/file-test2.txt")]
        ];
    }
}
