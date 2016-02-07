<?php
namespace WebStream\IO;

use WebStream\Exception\Extend\IOException;
use WebStream\Exception\Extend\InvalidArgumentException;

/**
 * InputStream
 * @author Ryuichi TANAKA.
 * @since 2016/02/05
 * @version 0.7
 */
class InputStream
{
    /**
     * @var resource 入力ストリーム
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
     * @param resource $stream 入力ストリーム
     */
    public function __construct($stream)
    {
        if (!is_resource($stream)) {
            throw new IOException("Input stream must be type of Resource.");
        }

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
    public function close()
    {
        if ($this->stream === null) {
            return;
        }

        if (@flose($this->stream) === false) {
            throw new IOException("Cannot close " . $this->file->getAbsoluteFilePath() . " cause by " . $php_errormsg);
        }

        $this->stream = null;
    }

    /**
     * 入力ストリームからデータを読み込む
     * 引数に数値を指定した場合、指定数値バイトだけ読み込む
     * @param int length 読み込みバイト数
     * @return string 読み込みデータ
     * @throws InvalidArgumentException
     */
    public function read($length = null)
    {
        if ($this->eof()) {
            return -1;
        }

        $out = "";
        if ($length === null) {
            while (!$this->eof()) {
                // InputStreamでは固定値でしか読み込ませない
                $out .= fread($this->stream, 8192);
                $this->cursorPosition = ftell($this->stream);
            }
        } else {
            if (!is_int($length)) {
                throw new InvalidArgumentException("Stream read must be a numeric value.");
            }

            // $lengthだけ指定して読み込む
            $out = fread($this->stream, $length);
            $this->cursorPosition = ftell($this->stream);
        }

        return $out;
    }

    /**
     * 入力ストリームから指定バイト数後方へポインタを移動する
     * @param int $pos 後方への移動バイト数(負数の場合は前方へ移動)
     * @return int $skipNum 移動したバイト数、移動に失敗した場合-1
     */
    public function skip(int $pos)
    {
        // 現在のポインタ位置から$posだけ後方へ移動
        // シークに対応していないファイルシステムの場合、-1を返す
        if (fseek($this->stream, $pos, SEEK_CUR) === -1) {
            return -1;
        }

        $start = $this->cursorPosition;
        $this->cursorPosition = ftell($this->stream);

        $skipNum = 0;
        if ($start > $this->cursorPosition) {
            // 前方へ移動
            $skipNum = $start - $this->cursorPosition;
        } else {
            // 後方へ移動
            $skipNum = $this->cursorPosition - $start;
        }

        return $skipNum;
    }

    /**
     * 入力ストリームの現在位置にmarkを設定する
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

        // ポインタ位置をmark位置に移動
        fseek($this->stream, SEEK_SET, $this->markedPosition);
        // mark位置を初期値に戻す
        $this->markedPosition = 0;
    }

    /**
     * EOFかどうか返却する
     * @return bool EOFならtrue
     */
    public function eof()
    {
        return feof($this->stream);
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
