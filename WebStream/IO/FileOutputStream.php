<?php
namespace WebStream\IO;

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
     * @throws InvalidArgumentException
     * @throws IOException
     */
    public function __construct($file, bool $isAppend = false)
    {
        if ($file instanceof File) {
            $this->file = $file;
        } elseif (is_string($file)) {
            $this->file = new File($file);
        } else {
            throw new InvalidArgumentException("Invalid argument type: " . $file);
        }

        $mode = $isAppend ? 'ab' : 'wb';
        $stream = @fopen($this->file->getAbsoluteFilePath(), $mode);

        if (!is_resource($stream) || $stream === false) {
            throw new IOException("Unable open " . $this->file->getAbsoluteFilePath());
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
