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
     * constructor
     * @param mixed $file ファイルオブジェクトまたはファイルパス
     */
    public function __construct($file)
    {
        parent::__construct(new FileInputStream($file));
    }

    /**
     * ファイルを読み込む
     * @return string ファイル内容
     */
    public function read()
    {
        $out = "";
        while (($data = $this->stream->read(8192)) !== null) {
            $out .= $data;
        }

        return $out;
    }
}
