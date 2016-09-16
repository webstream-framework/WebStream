<?php
namespace WebStream\IO;

use WebStream\Exception\Extend\IOException;

/**
 * File
 * 状態を表さないもの(ファイルパス等)はキャッシュし
 * 状態を表すもの(存在チェク、ファイル種別、権限等)はキャッシュクリアし都度取得する
 * @author Ryuichi TANAKA.
 * @since 2016/02/05
 * @version 0.7
 */
class File
{
    /**
     * @var string ファイルパス
     */
    private $filePath;

    /**
     * @var string ファイル名
     */
    private $fileName;

    /**
     * @var string ファイル拡張子
     */
    private $fileExt;

    /**
     * constructor
     * @param string $filepath ファイルパス
     */
    public function __construct(string $filePath)
    {
        // realpathを含めてキャッシュクリア
        clearstatcache(true);

        $this->filePath = $filePath;
        $this->fileName = basename($this->filePath);
        $this->fileExt = pathinfo($this->filePath, PATHINFO_EXTENSION);
    }

    /**
     * ファイル名を返却する
     * @return string ファイル名
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * ファイル拡張子を返却する
     * @return string ファイル拡張子
     */
    public function getFileExtension()
    {
        return $this->fileExt;
    }

    /**
     * ファイルパスを返却する
     * シンボリックリンクの場合、シンボリックリンクファイルパスを返却する
     * @return string ファイルパス
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * ファイルパスを返却する
     * シンボリックリンクの場合、実ファイルパスを返却する
     * @throws IOException
     * @return string ファイルパス
     */
    public function getAbsoluteFilePath()
    {
        if ($this->isLink()) {
            $filePath = $this->filePath;
            while (@is_link($filePath)) {
                $linkPath = readlink($filePath);
                if ($linkPath === false) {
                    throw new IOException("Symbolic link read error: " . $filePath);
                }
                $filePath = $linkPath;
            }

            return $filePath;
        }

        return $this->getFilePath();
    }

    /**
     * 読み込み権限があるか返却する
     * @return bool ファイルが存在し、読み込み権限があればtrue
     */
    public function isReadable()
    {
        // Fileオブジェクト作成後に属性が変わることを考慮しキャッシュクリアする
        clearstatcache();

        return @is_readable($this->filePath);
    }

    /**
     * 書き込み権限があるか返却する
     * @return bool ファイルが存在し、書き込み権限があればtrue
     */
    public function isWritable()
    {
        // Fileオブジェクト作成後に属性が変わることを考慮しキャッシュクリアする
        clearstatcache();

        return @is_writable($this->filePath);
    }

    /**
     * 実行権限があるか返却する
     * @return bool ファイルが存在し、実行権限があればtrue
     */
    public function isExecutable()
    {
        // Fileオブジェクト作成後に属性が変わることを考慮しキャッシュクリアする
        clearstatcache();

        return @is_executable($this->filePath);
    }

    /**
     * ファイルかどうか
     * @return bool ファイルならtrue
     */
    public function isFile()
    {
        clearstatcache();

        return is_file($this->filePath);
    }

    /**
     * ディレクトリかどうか
     * @return bool ディレクトリならtrue
     */
    public function isDirectory()
    {
        clearstatcache();

        return is_dir($this->filePath);
    }

    /**
     * リンクかどうか
     * @return bool リンクならtrue
     */
    public function isLink()
    {
        // Fileオブジェクト作成後に属性が変わることを考慮しキャッシュクリアする
        clearstatcache();

        return @is_link($this->filePath);
    }

    /**
     * ファイルサイズを返却する
     * ファイルが存在しなければ0
     * @return int ファイルサイズ
     */
    public function length()
    {
        $length = 0;

        if ($this->exists()) {
            $filePath = $this->getAbsoluteFilePath();
            $length = filesize($filePath);

            if ($length === false) {
                throw new IOException("Cannot get filesize of " . $filePath);
            }
        }

        return $length;
    }

    /**
     * ファイル(ディレクトリ、リンク含む)が存在するか
     * @return bool 存在すればtrue
     */
    public function exists()
    {
        // Fileオブジェクト作成後に属性が変わることを考慮しキャッシュクリアする
        clearstatcache();

        return $this->isLink() || $this->isDirectory() || $this->isFile();
    }

    /**
     * ファイルを削除する
     * @return bool 削除結果
     */
    public function delete()
    {
        // Fileオブジェクト作成後に属性が変わることを考慮しキャッシュクリアする
        clearstatcache();

        $isDeleted = false;
        if ($this->isWritable()) {
            if ($this->isDirectory()) {
                $isDeleted = rmdir($this->filePath);
            } else {
                $isDeleted = unlink($this->filePath);
            }
        }

        return $isDeleted;
    }

    /**
     * ファイルをリネームする
     * @param string $destPath 変更後ファイル名
     * @return bool リネーム結果
     */
    public function renameTo($destPath)
    {
        $dirname = dirname($destPath);
        $dir = new File($dirname);
        if (!$dir->isWritable()) {
            throw new IOException("Cannot writable: " . $destPath);
        }
        $dirPath = $dir->getFilePath();
        $absDestPath = $dirPath . "/" . basename($destPath);

        return rename($this->filePath, $absDestPath);
    }

    /**
     * ファイル更新日時を返却する
     * @return int ファイル更新日時
     */
    public function lastModified()
    {
        return $this->exists() ? filemtime($this->filePath) : 0;
    }
}
