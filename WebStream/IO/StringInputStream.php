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
    public function __construct($str)
    {
        parent::__construct($str);
        $this->length = strlen($str);
    }

    /**
     * 入力ストリームを閉じる
     */
    public function close()
    {
        $this->stream = null;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length = null)
    {
        if ($this->eof()) {
            return -1;
        }

        $out = "";
        if ($length === null) {
            $length = 1;
            $out = substr($this->stream, $this->cursorPosition, $length);
            $this->cursorPosition += $length;
        } else {
            // $lengthがファイル終端を越えないようにする
            if (($this->cursorPosition + $length) > $this->length) {
                $length = $this->length - $this->cursorPosition;
            }

            $out = substr($this->stream, $this->cursorPosition, $length);
            $this->cursorPosition += $length;
        }

        return $out;
    }

    public function readLine()
    {
        if ($this->eof()) {
            return null;
        }

        // 処理対象の残りのバイト数
        $targetLength = $this->length - $this->cursorPosition;

        // 処理対象の文字列
        $text = substr($this->stream, $this->cursorPosition, $targetLength);
        $lengthEOL = strlen(PHP_EOL);
        $notLinePart = strstr($text, PHP_EOL);

        // 残りの文字列に改行がない場合は0を設定
        $notLinePartLength = $notLinePart === false ? 0 : strlen($notLinePart);
        $offset = $targetLength - $notLinePartLength;
        $out = substr($text, 0, $offset);
        $this->skip($offset + $lengthEOL);

        return $out;
    }

    /**
     * {@inheritdoc}
     */
    public function skip(int $pos)
    {
        $start = $this->cursorPosition;
        $this->cursorPosition += $pos;

        if ($this->eof()) {
            return -1;
        }

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
     * {@inheritdoc}
     */
    public function eof()
    {
        return $this->cursorPosition > $this->length - 1;
    }

    /**
     * {@inheritdoc}
     */
    public function isMarkSupported()
    {
        return true;
    }
}
