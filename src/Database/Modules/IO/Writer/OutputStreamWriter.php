<?php
namespace WebStream\IO\Writer;

use WebStream\IO\OutputStream;
use WebStream\Exception\Extend\InvalidArgumentException;

/**
 * OutputStreamWriter
 * @author Ryuichi TANAKA.
 * @since 2016/02/24
 * @version 0.7
 */
class OutputStreamWriter
{
    /**
     * @var OutputStream 出力ストリーム
     */
    protected $stream;

    /**
     * constructor
     * @param OutputStream $stream 出力ストリーム
     */
    public function __construct(OutputStream $stream)
    {
        $this->stream = $stream;
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * バッファリングしているすべての出力バイトを書き出し、出力ストリームを閉じる
     * @throws WebStream\Exception\Extend\IOException
     */
    public function close()
    {
        $this->stream->close();
    }

    /**
     * 出力ストリームに書き出す
     * @param mixed $buf 出力データ
     * @throws WebStream\Exception\Extend\IOException
     */
    public function write($buf)
    {
        $args = func_get_args();
        $off = array_key_exists(1, $args) ? $args[1] : null;
        $len = array_key_exists(2, $args) ? $args[2] : null;

        $this->stream->write($buf, $off, $len);
    }

    /**
     * バッファリングしているすべての出力バイトを出力ストリームを閉じずに強制的に書き出す
     * writeを実行した時点では書き出されておらず、flushした時点ですべて書き出す
     * @throws WebStream\Exception\Extend\IOException
     */
    public function flush()
    {
        $this->stream->flush();
    }

    /**
     * 改行を書き出す
     * @throws WebStream\Exception\Extend\IOException
     */
    public function newLine()
    {
        $this->stream->write(PHP_EOL);
    }
}
