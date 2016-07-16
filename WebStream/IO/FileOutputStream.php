<?php
namespace WebStream\IO;

use WebStream\IO\OutputStream;
use WebStream\Exception\Extend\InvalidArgumentException;
use WebStream\Exception\Extend\IOException;

/**
 * FileOutputStream
 * @author Ryuichi TANAKA.
 * @since 2016/02/24
 * @version 0.7
 */
class FileOutputStream extends OutputStream
{
    /**
     * @var File ファイルオブジェクト
     */
    protected $file;

    /**
     * constructor
     * @param mixed $file ファイルオブジェクトまたはファイルパス
     * @param bool $isAppend 追記フラグ
     * @throws InvalidArgumentException
     * @throws IOException
     */
    public function __construct($file, bool $isAppend = false)
    {
        $filepath = null;
        if ($file instanceof File) {
            $this->file = $file;
            $filepath = $this->file->getFilePath();
        } elseif (is_string($file)) {
            if (!file_exists($file)) {
                $dirname = dirname($filepath);
                $dir = new File($dirname);
                if (!$dir->isWritable()) {
                    throw new IOException("Cannot writable: " . $filepath);
                }
            }
            $this->file = new File($file);
            $filepath = $this->file->getFilePath();
        } else {
            throw new InvalidArgumentException("Invalid argument type: " . $file);
        }

        $mode = $isAppend ? 'ab' : 'wb';
        $stream = fopen($filepath, $mode);

        if (!is_resource($stream) || $stream === false) {
            throw new IOException("Unable open " . $this->file->getFilePath());
        }

        if (!flock($stream, LOCK_EX | LOCK_NB)) {
            throw new IOException("Cannot lock file: " . $this->file->getFilePath());
        }

        parent::__construct($stream);
    }

    /**
     * {@inheritdoc}
     */
    public function write($buf, int $off = null, int $len = null)
    {
        $data = null;
        if ($off === null && $len === null) {
            $data = $buf;
        } elseif ($off !== null && $len === null) {
            $data = substr($buf, $off);
        } elseif ($off === null && $len !== null) {
            $data = substr($buf, 0, $len);
        } else {
            $data = substr($buf, $off, $len);
        }

        if (@fwrite($this->stream, $data) === false) {
            throw new IOException("Failed to write stream.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        if ($this->stream === null) {
            return;
        }

        $this->flush();

        // PHP5.3.2以降はfcloseではロック解放されなくなり、明示的に開放する必要がある
        flock($this->stream, LOCK_UN);

        if (get_resource_type($this->stream) !== 'Unknown' && fclose($this->stream) === false) {
            throw new IOException("Cannot close output stream.");
        }

        $this->stream = null;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        if (@fflush($this->stream) === false) {
            throw new IOException("Failed to flush.");
        }
    }
}
