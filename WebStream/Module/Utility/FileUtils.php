<?php
namespace WebStream\Module\Utility;

/**
 * FileUtls
 * ファイル操作に関するUtility
 * @author Ryuichi Tanaka
 * @since 2015/12/26
 * @version 0.7
 */
trait FileUtils
{
    use ApplicationUtils;

    /**
     * 設定ファイルをパースする
     * @param string プロジェクトルートからの相対パス
     * @return hash 設定情報
     */
    public function parseConfig($filepath)
    {
        // 正規化した絶対パス
        $realpath = $this->getApplicationRoot() . DIRECTORY_SEPARATOR . $filepath;

        return file_exists($realpath) ? parse_ini_file($realpath) : null;
    }

    /**
     * ファイル検索イテレータを返却する
     * @param string ディレクトリパス
     * @return object イテレータ
     */
    public function getFileSearchIterator($path)
    {
        return new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::LEAVES_ONLY,
            \RecursiveIteratorIterator::CATCH_GET_CHILD // for Permission deny
        );
    }
}
