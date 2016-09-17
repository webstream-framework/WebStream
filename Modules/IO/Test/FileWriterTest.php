<?php
namespace WebStream\IO\Test;

require_once dirname(__FILE__) . '/../InputStream.php';
require_once dirname(__FILE__) . '/../OutputStream.php';
require_once dirname(__FILE__) . '/../File.php';
require_once dirname(__FILE__) . '/../FileInputStream.php';
require_once dirname(__FILE__) . '/../FileOutputStream.php';
require_once dirname(__FILE__) . '/../Reader/InputStreamReader.php';
require_once dirname(__FILE__) . '/../Reader/FileReader.php';
require_once dirname(__FILE__) . '/../Writer/OutputStreamWriter.php';
require_once dirname(__FILE__) . '/../Writer/FileWriter.php';
require_once dirname(__FILE__) . '/../Test/Providers/FileWriterProvider.php';
require_once dirname(__FILE__) . '/../Test/Modules/IOException.php';

use WebStream\IO\File;
use WebStream\IO\Reader\FileReader;
use WebStream\IO\Writer\FileWriter;
use WebStream\IO\Test\Providers\FileWriterProvider;

/**
 * FileWriterTest
 * @author Ryuichi TANAKA.
 * @since 2016/08/18
 * @version 0.7
 */
class FileWriterTest extends \PHPUnit_Framework_TestCase
{
    use FileWriterProvider;

    /**
     * 正常系
     * ファイルパスからファイルに書き込みできること
     * @test
     * @dataProvider writeProvider
     */
    public function okWriteFromFilePath($filePath, $content)
    {
        $file = new File($filePath);
        $file->delete();

        $writer = new FileWriter($filePath);
        $writer->write($content);
        $writer->close();

        $reader = new FileReader($filePath);
        $this->assertEquals($reader->read(), $content);
    }

    /**
     * 正常系
     * ファイルオブジェクトからファイルに書き込みできること
     * @test
     * @dataProvider writeProvider
     */
    public function okWriteFromFileObject($filePath, $content)
    {
        $file = new File($filePath);
        $file->delete();

        $writer = new FileWriter($file);
        $writer->write($content);
        $writer->close();

        $reader = new FileReader($filePath);
        $this->assertEquals($reader->read(), $content);
    }

    /**
     * 正常系
     * ファイルに追記できること
     * @test
     * @dataProvider writeAppendProvider
     */
    public function okWriteAppend($filePath, $content, $result)
    {
        $file = new File($filePath);
        $file->delete();

        $writer = new FileWriter($filePath);
        $writer->write($content);
        $writer->close();

        $writer = new FileWriter($filePath, true);
        $writer->write($content);
        $writer->close();

        $reader = new FileReader($filePath);
        $this->assertEquals($reader->read(), $result);
    }
}
