<?php
namespace WebStream\IO;

use WebStream\Exception\Extend\InvalidArgumentException;
use WebStream\Exception\Extend\IOException;

/**
 * FileInputStream
 * @author Ryuichi TANAKA.
 * @since 2016/02/05
 * @version 0.7
 */
class FileInputStream extends InputStream
{
    /**
     * @var File ファイルオブジェクト
     */
    protected $file;

    /**
     * constructor
     * @param mixed $file ファイルオブジェクトまたはファイルパス
     * @throws InvalidArgumentException
     * @throws IOException
     */
    public function __construct($file)
    {
        if ($file instanceof File) {
            $this->file = $file;
        } elseif (is_string($file)) {
            $this->file = new File($file);
        } else {
            throw new InvalidArgumentException("Unable to open file: " . $file);
        }

        // 読み込みはロックを掛けずダーティーリード
        $stream = fopen($this->file->getAbsoluteFilePath(), 'r');
        if (!is_resource($stream) || $stream === false) {
            throw new IOException("Unable open " . $this->file->getAbsoluteFilePath());
        }

        parent::__construct($stream);
    }

    /**
     * 入力ストリームを閉じる
     */
    public function close()
    {
        if ($this->stream === null) {
            return;
        }

        if (get_resource_type($this->stream) !== 'Unknown' && fclose($this->stream) === false) {
            throw new IOException("Cannot close input stream.");
        }

        $this->stream = null;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length = null)
    {
        if ($this->eof()) {
            return null;
        }

        $out = null;
        if ($length === null) {
            if (($out = @fread($this->stream, 1)) === false) {
                throw new IOException("Failed to read stream.");
            }
        } else {
            if (!is_int($length)) {
                throw new InvalidArgumentException("Stream read must be a numeric value.");
            }
            // ポインタ位置が負になった場合、警告が出てfalseを返す
            // ポインタの終端を越えた場合、読み込みを終了する
            // すでに終端位置の場合、空文字を返す
            if (($out = @fread($this->stream, $length)) === false) {
                throw new IOException("Failed to read stream.");
            }
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
        if ($this->eof()) {
            return null;
        }

        $out = fgets($this->stream);
        if ($out === false) {
            return null;
        }

        $this->cursorPosition = ftell($this->stream);

        return trim($out);
    }

    /**
     * {@inheritdoc}
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
    public function reset()
    {
        if (!$this->isMarkSupported()) {
            throw new IOException(get_class($this) . " does not support mark and reset.");
        }

        // ポインタ位置をmark位置に移動
        fseek($this->stream, SEEK_SET, $this->markedPosition);
        // mark位置を初期値に戻す
        $this->cursorPosition = $this->markedPosition;
        $this->markedPosition = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        return feof($this->stream);
    }

    /**
     * {@inheritdoc}
     */
    public function isMarkSupported()
    {
        return true;
    }
}
