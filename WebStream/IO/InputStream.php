<?php
namespace WebStream\IO;

use WebStream\Exception\Extend\IOException;

/**
 * InputStream
 * ftellの戻り値仕様により32ビット数値を返すため2GB以上のファイルを読み込むと動かなくなる
 * C言語のftell仕様依存
 * @author Ryuichi TANAKA.
 * @since 2016/02/05
 * @version 0.7
 */
abstract class InputStream
{
    /**
     * @var mixed 入力ストリーム
     */
    protected $stream;

    /**
     * @var int 現在のポインタ位置
     */
    protected $cursorPosition;

    /**
     * @var int markしたポインタ位置
     */
    protected $markedPosition;

    /**
     * constructor
     * @param mixed $stream 入力ストリーム
     */
    public function __construct($stream)
    {
        $this->stream = $stream;
        $this->cursorPosition = 0;
        $this->markedPosition = 0;
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * 入力ストリームを閉じる
     */
    abstract public function close();

    /**
     * 入力ストリームからデータを読み込む
     * 引数に数値を指定した場合、指定数値バイトだけ読み込む
     * @param int length 読み込みバイト数
     * @return string 読み込みデータ
     * @throws InvalidArgumentException
     * @throws IOException
     */
    abstract public function read($length = null);

    /**
     * 入力ストリームから指定バイト数後方へポインタを移動する
     * @param int $pos 後方への移動バイト数(負数の場合は前方へ移動)
     * @return int $skipNum 移動したバイト数、移動に失敗した場合-1
     */
    public function skip(int $pos)
    {
        return -1;
    }

    /**
     * EOFかどうか返却する
     * @return bool EOFならtrue、EOF以外またはリードエラーの場合はfalse
     */
    abstract public function eof();

    /**
     * 入力ストリームの現在位置にmarkを設定する
     * @param int マークするバイト位置
     * @throws IOException
     */
    public function mark()
    {
        if (!$this->isMarkSupported()) {
            throw new IOException(get_class($this) . " does not support mark.");
        }

        $this->markedPosition = $this->cursorPosition;
    }

    /**
     * 最後にmarkされた位置に再配置する
     * @throws IOException
     */
    public function reset()
    {
        if (!$this->isMarkSupported()) {
            throw new IOException(get_class($this) . " does not support mark and reset.");
        }

        // mark位置を初期値に戻す
        $this->cursorPosition = $this->markedPosition;
        $this->markedPosition = 0;
    }

    /**
     * mark機能をサポートしているかどうか
     * @return boolean マークをサポートしていればtrue
     */
    public function isMarkSupported()
    {
        return false;
    }
}
