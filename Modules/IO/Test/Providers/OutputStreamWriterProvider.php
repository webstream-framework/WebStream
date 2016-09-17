<?php
namespace WebStream\IO\Test\Providers;

use WebStream\IO\FileOutputStream;
use WebStream\IO\ConsoleOutputStream;

/**
 * OutputStreamWriterProvider
 * @author Ryuichi TANAKA.
 * @since 2016/08/19
 * @version 0.7
 */
trait OutputStreamWriterProvider
{
    public function writeProvider()
    {
        return [
            ["/tmp/outputstreamwriter-test1.txt"]
        ];
    }
}
