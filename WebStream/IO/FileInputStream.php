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
        } else if (is_string($file)) {
            $this->file = new File($file);
        } else {
            throw new InvalidArgumentException("Unable to open file: " . $file);
        }

        $stream = @fopen($this->file->getAbsoluteFilePath());
        if ($stream === false) {
            throw new IOException("Unable open " . $this->file->getAbsoluteFilePath() . " cause by " . $php_errormsg);
        }

        parent::__construct($stream);
    }

    /**
     * {@inheritdoc}
     */
    public function isMarkSupported()
    {
        return true;
    }
}
