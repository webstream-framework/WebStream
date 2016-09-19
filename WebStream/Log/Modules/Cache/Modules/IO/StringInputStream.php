<?php
namespace WebStream\IO;

use WebStream\Exception\Extend\InvalidArgumentException;
use WebStream\Exception\Extend\IOException;

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
     * @var bool 終端かどうか
     */
    private $isEOF = false;

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
        if ($this->stream === null) {
            return null;
        }

        if ($this->eof()) {
            return null;
        }

        // SkipでポインタをずらしただけではEOFにはならない、FileInputStream実装に合わせる
        // Skipでずらしたあとreadすると空文字を返し、もう一度readするとEOFを認識する
        // ファイルの終端より先にポインタをすすめることは「可能」＝書き込みと同じ
        // なので、現在の終端位置より先に進めてもEOF自体にはならない。進めた位置はEOFのひとつ前
        // だからもう一回readするとEOFに到達する。なのでskipを使ってもEOF到達できない
        if ($this->cursorPosition > $this->length - 1) {
            $this->isEOF = true;

            return "";
        }

        $out = "";
        if ($length === null) {
            $length = 1;
            $out = substr($this->stream, $this->cursorPosition, 1);
            $this->cursorPosition += 1;
        } else {
            if (!is_int($length)) {
                throw new InvalidArgumentException("Stream read must be a numeric value.");
            }

            // $lengthがファイル終端を越えないようにする
            if (($this->cursorPosition + $length) > $this->length) {
                $length = $this->length - $this->cursorPosition;
            }

            $out = substr($this->stream, $this->cursorPosition, $length);
            $this->cursorPosition += $length;
        }

        return $out;
    }

    /**
     * 入力ストリームから行単位でデータを読み込む
     * 末尾に改行コードは含まない
     * @return string 読み込みデータ
     */
    public function readLine()
    {
        if ($this->stream === null) {
            return null;
        }

        if ($this->eof()) {
            return null;
        }

        // 処理対象の残りのバイト数
        $targetLength = $this->length - $this->cursorPosition;

        // 処理対象の文字列
        $text = substr($this->stream, $this->cursorPosition, $targetLength);
        $lengthEOL = strlen(PHP_EOL);
        $notLinePart = strstr($text, PHP_EOL);

        $notLinePartLength = 0;
        if ($notLinePart !== false) {
            $notLinePartLength = strlen($notLinePart);
        }

        $offset = $targetLength - $notLinePartLength;
        $out = substr($text, 0, $offset);
        $out = $out === false ? null : $out;
        $this->skip($offset + $lengthEOL);

        return $out;
    }

    /**
     * {@inheritdoc}
     */
    public function skip(int $pos)
    {
        if ($this->stream === null) {
            return -1;
        }

        // ファイル終端到達後、skipを実行すると後方にポインタが移動する
        // このときEOFだったものがEOFでなくなる
        $start = $this->cursorPosition;

        // 現在位置が負になった場合は-1を返して終了
        if ($this->cursorPosition + $pos < 0) {
            return -1;
        }

        $this->cursorPosition += $pos;
        $this->isEOF = false;

        // skipした実際のバイト数
        $skipNum = 0;
        if ($start > $this->cursorPosition) {
            // 後方へ移動
            $skipNum = $start - $this->cursorPosition;
        } else {
            // 前方へ移動
            $skipNum = $this->cursorPosition - $start;
        }

        return $skipNum;
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        return $this->isEOF;
    }

    /**
     * {@inheritdoc}
     */
    public function isMarkSupported()
    {
        return true;
    }
}
