<?php
namespace WebStream\IO\Test;

require_once dirname(__FILE__) . '/../InputStream.php';
require_once dirname(__FILE__) . '/../FileInputStream.php';
require_once dirname(__FILE__) . '/../StringInputStream.php';
require_once dirname(__FILE__) . '/../Reader/InputStreamReader.php';
require_once dirname(__FILE__) . '/../Test/Providers/InputStreamReaderProvider.php';
require_once dirname(__FILE__) . '/../Test/Modules/IOException.php';
require_once dirname(__FILE__) . '/../Test/Modules/InvalidArgumentException.php';

use WebStream\IO\Reader\InputStreamReader;
use WebStream\IO\Test\Providers\InputStreamReaderProvider;

/**
 * InputStreamReaderTest
 * @author Ryuichi TANAKA.
 * @since 2016/08/18
 * @version 0.7
 */
class InputStreamReaderTest extends \PHPUnit_Framework_TestCase
{
    use InputStreamReaderProvider;

    /**
     * 正常系
     * バイト単位で読み込みできること
     * EOFを超えるサイズの読み込み時は、ファイルの場合改行が含まれる
     * @test
     * @dataProvider readCharProvider
     */
    public function okReadChar($stream, $result, $byteLength)
    {
        $reader = new InputStreamReader($stream);
        $this->assertEquals($reader->read($byteLength), $result);
    }

    /**
     * 正常系
     * 行単位でデータが読み込めること
     * @test
     * @dataProvider readLineProvider
     */
    public function okReadLine($stream, $result1, $result2)
    {
        $reader = new InputStreamReader($stream);
        $this->assertEquals($reader->readLine(), $result1);
        $this->assertEquals($reader->readLine(), $result2);
        $this->assertEquals($reader->readLine(), null);
    }

    /**
     * 正常系
     * 入力ストリームをクローズできること
     * @test
     * @dataProvider closeProvider
     */
    public function okClose($stream)
    {
        $reader = new InputStreamReader($stream);
        $reader->close();
        $this->assertNull($reader->read());
    }

    /**
     * 正常系
     * 指定バイト数だけスキップできること
     * EOFを超えるサイズの読み込み時は、ファイルの場合改行が含まれる
     * @test
     * @dataProvider skipProvider
     */
    public function okSkip($stream, $result, $pos)
    {
        $reader = new InputStreamReader($stream);
        $this->assertEquals($reader->skip($pos), $pos);
        $this->assertEquals($reader->read(), $result);
    }

    /**
     * 正常系
     * 終端を越えたスキップをしたとき
     * 1回目のreadは空文字を返し、2回目のreadはnullを返すこと
     * @test
     * @dataProvider overSkipAndReadProvider
     */
    public function okOverSkipAndRead($stream, $skipNum)
    {
        $reader = new InputStreamReader($stream);
        $this->assertEquals($reader->skip($skipNum), $skipNum);
        $this->assertEmpty($reader->read());
        $this->assertNull($reader->read());
    }

    /**
     * 正常系
     * ポインタを後方に移動できること
     * @test
     * @dataProvider frontSkipProvider
     */
    public function okFrontSkip($stream, $skipNum1, $skipNum2, $result)
    {
        $reader = new InputStreamReader($stream);
        $reader->skip($skipNum1);
        $reader->skip($skipNum2);
        $this->assertEquals($reader->read(), $result);
    }

    /**
     * 正常系
     * ポインタ位置が負になった場合、移動量は常に-1になること
     * @test
     * @dataProvider overFrontSkipProvider
     */
    public function okOverFrontSkip($stream, $pos)
    {
        $reader = new InputStreamReader($stream);
        $this->assertEquals($reader->skip($pos), -1);
    }

    /**
     * 正常系
     * リセットすると初期位置にポインタが移動すること
     * @test
     * @dataProvider resetProvider
     */
    public function okReset($stream, $skipNum, $result)
    {
        $reader = new InputStreamReader($stream);
        $reader->skip($skipNum);
        $reader->reset();
        $this->assertEquals($reader->read(), $result);
    }

    /**
     * 正常系
     * リセットするとマーク位置にポインタが移動すること
     * @test
     * @dataProvider markAndResetProvider
     */
    public function okMarkAndReset($stream, $skipNum, $result)
    {
        $reader = new InputStreamReader($stream);
        $reader->skip($skipNum);
        $reader->mark();
        $reader->reset();
        $this->assertEquals($reader->read(), $result);
    }

    /**
     * 異常系
     * 読み込みサイズに不正値を渡した時、例外が発生すること
     * @test
     * @dataProvider invalidLengthProvider
     * @expectedException WebStream\Exception\Extend\InvalidArgumentException
     */
    public function ngInvalidLength($stream)
    {
        $reader = new InputStreamReader($stream);
        $reader->read("dummy");
        $this->assertTrue(false);
    }
}
