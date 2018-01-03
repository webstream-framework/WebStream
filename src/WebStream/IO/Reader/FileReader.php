<?php
namespace WebStream\IO\Reader;

use WebStream\IO\FileInputStream;

/**
 * FileReader
 * @author Ryuichi TANAKA.
 * @since 2016/02/05
 * @version 0.7
 */
class FileReader extends InputStreamReader
{
    /**
     * @var int バッファリングサイズ
     */
    private $bufferSize;

    /**
     * constructor
     * @param mixed $file ファイルオブジェクトまたはファイルパス
     * @param int $bufferSize バッファリングサイズ
     */
    public function __construct($file, int $bufferSize = 8192)
    {
        parent::__construct(new FileInputStream($file));
        $this->bufferSize = $bufferSize;
    }

    /**
     * ファイルを読み込む
     * @return string ファイル内容
     */
    public function read()
    {
        $out = "";
        while (($data = $this->stream->read($this->bufferSize)) !== null) {
            $out .= $data;
        }

        return $out;
    }
}
