<?php
namespace WebStream\IO\Reader;

use WebStream\IO\InputStream;

/**
 * InputStreamReader
 * @author Ryuichi TANAKA.
 * @since 2016/02/05
 * @version 0.7
 */
class InputStreamReader
{
    /**
     * @var InputStream 入力ストリーム
     */
    protected $stream;

    /**
     * constructor
     * @param InputStream $stream 入力ストリーム
     */
    public function __construct(InputStream $stream)
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
     * 読み込んでいる入力ストリームを閉じる
     */
    public function close()
    {
        $this->stream->close();
    }

    /**
     * 入力ストリームからデータを読み込む
     * @return string 読み込みデータ
     */
    public function read()
    {
        $args = func_get_args();
        $length = count($args) === 1 ? $args[0] : null;

        return $this->stream->read($length);
    }

    /**
     * 入力ストリームから行単位でデータを読み込む
     * 末尾に改行コードは含まない
     * @return string 読み込みデータ
     */
    public function readLine()
    {
        return $this->stream->readLine();
    }

    /**
     * 入力ストリームから指定バイト数後方へポインタを移動する
     * @param int $pos 後方への移動バイト数(負数の場合は前方へ移動)
     * @return int $skipNum 移動したバイト数、移動に失敗した場合-1
     */
    public function skip(int $pos)
    {
        return $this->stream->skip($pos);
    }

    /**
     * 最後にmarkされた位置に再配置する
     * @throws IOException
     */
    public function reset()
    {
        $this->stream->reset();
    }
}
