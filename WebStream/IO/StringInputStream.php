<?php
namespace WebStream\IO;

/**
 * StringInputStream
 * @author Ryuichi TANAKA.
 * @since 2016/02/07
 * @version 0.7
 */
class StringInputStream extends InputStream
{
    /**
     * @var int 文字列長
     */
    private $length;

    /**
     * construct
     * @param string $str 文字列
     */
    public function __construct(string $str)
    {
        $this->cursorPosition = 0;
        $this->markedPosition = 0;

        // 文字列をストリームとして扱う
        $this->stream = $str;
        $this->length = count($str);
    }

    /**
     * 入力ストリームを閉じる
     */
    public function close()
    {
        $this->stream = null;
    }

    /**
     * 入力ストリームからデータを読み込む
     * 引数に数値を指定した場合、指定数値バイトだけ読み込む
     * @param int length 読み込みバイト数
     * @return string 読み込みデータ
     */
    public function read($length = null)
    {
        if ($this->eof()) {
            return -1;
        }

        $out = "";
        if ($length === null) {
            $endPosition = $this->length > $this->cursorPosition ?
                $this->cursorPosition : $this->length;

            $out = substr($this->stream, $endPosition);
        } else {
            // $lengthがファイル終端を越えないようにする
            if ($this->length > $this->cursorPosition + $length) {
                $length = $this->length - $this->cursorPosition;
            }

            $out = substr($this->stream, $this->cursorPosition, $length);
        }

        return $out;
    }

    /**
     * {@inheritdoc}
     */
    public function skip(int $pos)
    {
        // 文字列長より後方を指定した場合、-1を返す
        if ($this->cursorPosition + $pos > $this->length - 1) {
            return -1;
        }

        $start = $this->cursorPosition;
        $this->cursorPosition = $pos;

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
     */
    public function mark()
    {
        $this->markedPosition = $this->cursorPosition;
    }

    /**
     * 最後にmarkされた位置に再配置する
     */
    public function reset()
    {
        $this->markedPosition = 0;
    }

    public function eof()
    {
        return $this->cursorPosition >= $this->length - 1;
    }

    /**
     * {@inheritdoc}
     */
    public function isMarkSupported()
    {
        return true;
    }
}
