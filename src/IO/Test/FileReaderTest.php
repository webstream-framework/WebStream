<?php
namespace WebStream\IO\Test;

require_once dirname(__FILE__) . '/../InputStream.php';
require_once dirname(__FILE__) . '/../File.php';
require_once dirname(__FILE__) . '/../FileInputStream.php';
require_once dirname(__FILE__) . '/../Reader/InputStreamReader.php';
require_once dirname(__FILE__) . '/../Reader/FileReader.php';
require_once dirname(__FILE__) . '/../Test/Providers/FileReaderProvider.php';
require_once dirname(__FILE__) . '/../Test/Modules/IOException.php';

use WebStream\IO\File;
use WebStream\IO\Reader\FileReader;
use WebStream\IO\Test\Providers\FileReaderProvider;

/**
 * FileReaderTest
 * @author Ryuichi TANAKA.
 * @since 2016/08/18
 * @version 0.7
 */
class FileReaderTest extends \PHPUnit\Framework\TestCase
{
    use FileReaderProvider;

    /**
     * 正常系
     * ファイルパスからファイルが読み込めること
     * @test
     * @dataProvider readProvider
     */
    public function okReadFromFilePath($filePath, $result)
    {
        $reader = new FileReader($filePath);
        $this->assertEquals($reader->read(), $result);
    }

    /**
     * 正常系
     * ファイルオブジェクトからファイルが読み込めること
     * @test
     * @dataProvider readProvider
     */
    public function okReadFromFileObject($filePath, $result)
    {
        $file = new File($filePath);
        $reader = new FileReader($file);
        $this->assertEquals($reader->read(), $result);
    }
}
