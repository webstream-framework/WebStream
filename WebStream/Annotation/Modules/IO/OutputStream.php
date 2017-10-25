<?php
namespace WebStream\IO;

/**
 * OutputStream
 * @author Ryuichi TANAKA.
 * @since 2016/02/24
 * @version 0.7
 */
abstract class OutputStream
{
    /**
     * @var mixed 出力ストリーム
     */
    protected $stream;

    /**
     * constructor
     * @param mixed $stream 出力ストリーム
     */
    public function __construct($stream)
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
     * 出力ストリームに書き出す
     * @param mixed $buf 出力データ
     * @param int $off データ開始位置
     * @param int $len 書き出すバイト数
     * @throws WebStream\Exception\Extend\IOException
     */
    abstract public function write($buf, int $off = null, int $len = null);

    /**
     * バッファリングしているすべての出力バイトを書き出し、出力ストリームを閉じる
     * @throws WebStream\Exception\Extend\IOException
     */
    abstract public function close();

    /**
     * バッファリングしているすべての出力バイトを出力ストリームを閉じずに強制的に書き出す
     * writeを実行した時点では書き出されておらず、flushした時点ですべて書き出す
     * @throws WebStream\Exception\Extend\IOException
     */
    abstract public function flush();
}
