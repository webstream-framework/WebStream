<?php
namespace WebStream\IO\Writer;

use WebStream\IO\FileOutputStream;
use WebStream\Exception\Extend\IOException;

/**
 * FileWriter
 * @author Ryuichi TANAKA.
 * @since 2016/02/24
 * @version 0.7
 */
class FileWriter extends OutputStreamWriter
{
    /**
     * constructor
     * @param mixed $file ファイルオブジェクトまたはファイルパス
     */
    public function __construct($file, bool $isAppend = false, int $bufferSize = null)
    {
        parent::__construct(new FileOutputStream($file, $isAppend));

        // fwriteのデフォルトバッファリングサイズは8KBなので、指定無しの場合は8KBになる
        // また、同じストリームに対して出力を行うプロセスが複数ある場合、8KBごとに停止する
        // see: http://php.net/manual/ja/function.stream-set-write-buffer.php
        if ($bufferSize !== null && stream_set_write_buffer($this->stream, $bufferSize) !== 0) {
            throw new IOException("Failed to change the buffer size.");
        }
    }

    /**
     * ファイルに書き込む
     * @param mixed $data 書き込みデータ
     */
    public function write($data)
    {
        $this->stream->write($data);
    }
}
