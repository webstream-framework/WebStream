<?php
namespace WebStream\IO\Test;

require_once dirname(__FILE__) . '/../File.php';
require_once dirname(__FILE__) . '/../Writer/OutputStreamWriter.php';
require_once dirname(__FILE__) . '/../Writer/SimpleFileWriter.php';
require_once dirname(__FILE__) . '/../Test/Providers/FileProvider.php';
require_once dirname(__FILE__) . '/../Test/Modules/IOException.php';

use WebStream\IO\File;
use WebStream\IO\Writer\SimpleFileWriter;
use WebStream\IO\Test\Providers\FileProvider;

/**
 * FileTest
 * @author Ryuichi TANAKA.
 * @since 2016/08/19
 * @version 0.7
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    use FileProvider;

    /**
     * 正常系
     * ファイル名を取得できること
     * @test
     * @dataProvider fileProvider
     */
    public function okFileName($file)
    {
        $this->assertEquals($file->getFileName(), "file-test1.txt");
    }

    /**
     * 正常系
     * ファイル拡張子を取得できること
     * @test
     * @dataProvider fileProvider
     */
    public function okFileExtension($file)
    {
        $this->assertEquals($file->getFileExtension(), "txt");
    }

    /**
     * 正常系
     * ファイルパスを取得できること
     * @test
     * @dataProvider fileProvider
     */
    public function okFilePath($file)
    {
        $this->assertEquals($file->getFilePath(), dirname(__FILE__) . "/Providers/../Fixtures/" . $file->getFileName());
    }

    /**
     * 正常系
     * 絶対ファイルパスを取得できること
     * @test
     * @dataProvider fileLinkProvider
     */
    public function okFileAbsolutePath($file, $linkPath)
    {
        symlink($file->getFilePath(), $linkPath);
        $linkFile = new File($linkPath);
        $this->assertEquals($file->getAbsoluteFilePath(), $linkFile->getAbsoluteFilePath());
        unlink($linkPath);
    }

    /**
     * 正常系
     * ファイルに読み込み権限をチェックできること
     * @test
     * @dataProvider fileProvider
     */
    public function okFileReadable($file)
    {
        chmod($file->getFilePath(), 0444);
        $this->assertTrue($file->isReadable());
    }

    /**
     * 正常系
     * ファイルに書き込み権限をチェックできること
     * @test
     * @dataProvider fileProvider
     */
    public function okFileWritable($file)
    {
        chmod($file->getFilePath(), 0666);
        $this->assertTrue($file->isWritable());
    }

    /**
     * 正常系
     * ファイルに実行権限をチェックできること
     * @test
     * @dataProvider fileProvider
     */
    public function okFileExecutable($file)
    {
        chmod($file->getFilePath(), 0555);
        $this->assertTrue($file->isExecutable());
    }

    /**
     * 正常系
     * ファイルであるかどうかチェックできること
     * @test
     * @dataProvider fileProvider
     */
    public function okFile($file)
    {
        $this->assertTrue($file->exists());
        $this->assertTrue($file->isFile());
    }

    /**
     * 正常系
     * ディレクトリであるかどうかチェックできること
     * @test
     * @dataProvider directoryProvider
     */
    public function okDirectory($file)
    {
        $this->assertTrue($file->exists());
        $this->assertTrue($file->isDirectory());
    }

    /**
     * 正常系
     * リンクであるかどうかチェックできること
     * @test
     * @dataProvider fileLinkProvider
     */
    public function okLink($file, $linkPath)
    {
        symlink($file->getFilePath(), $linkPath);
        $linkFile = new File($linkPath);
        $this->assertTrue($linkFile->exists());
        $this->assertTrue($linkFile->isLink());
        unlink($linkPath);
    }

    /**
     * 正常系
     * ファイルサイズを取得できること
     * @test
     * @dataProvider fileProvider
     */
    public function okFileSize($file)
    {
        $this->assertEquals($file->length(), 5);
    }

    /**
     * 正常系
     * ファイルを削除できること
     * @test
     * @dataProvider tmpFileProvider
     */
    public function okFileDelete($file)
    {
        $writer = new SimpleFileWriter($file->getFilePath());
        $writer->write("test");
        $this->assertTrue($file->delete());
    }

    /**
     * 正常系
     * ディレクトリを削除できること
     * @test
     * @dataProvider tmpDirectoryProvider
     */
    public function okDirectoryDelete($file)
    {
        mkdir($file->getFilePath(), 0777);
        $this->assertTrue($file->delete());
    }

    /**
     * 正常系
     * ファイルサイズを取得できること
     * @test
     * @dataProvider tmpFileProvider
     */
    public function okFileRename($file)
    {
        $writer = new SimpleFileWriter($file->getFilePath());
        $writer->write("test");
        $this->assertTrue($file->renameTo("/tmp/file-test-rename.txt"));

        $renameFile = new File("/tmp/file-test-rename.txt");
        $renameFile->delete();
    }

    /**
     * 正常系
     * ファイル最終更新日時を取得できること
     * @test
     * @dataProvider fileProvider
     */
    public function okLastModified($file)
    {
        $this->assertEquals(gettype($file->lastModified()), "integer");
    }

    /**
     * 異常系
     * ファイルのリネームに失敗すること
     * @test
     * @dataProvider renameFailureProvider
     * @expectedException WebStream\Exception\Extend\IOException
     */
    public function ngFileRename($file)
    {
        chmod($file->getFilePath(), 0444);
        $file->renameTo("/tmp/file-test-rename/" . $file->getFileName());
    }
}
