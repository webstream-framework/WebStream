<?php
namespace WebStream\Module;

use WebStream\Exception\Extend\IOException;

/**
 * Cacheクラス
 * @author Ryuichi Tanaka
 * @since 2011/10/08
 * @version 0.4.1
 */
class Cache
{
    use Utility;

    /** キャッシュの保存ディレクトリ */
    private $savePath;

    /**
     * コンストラクタ
     * @param string キャッシュの保存ディレクトリパス
     */
    public function __construct($savePath = null)
    {
        $this->savePath = $savePath;
        if ($this->savePath === null) {
            $this->savePath = $this->getTemporaryDirectory();
        }
    }

    /**
     * キャッシュを取得する
     * @param string キャッシュID
     * @return string キャッシュデータ
     */
    public function get($id)
    {
        $cache = $this->cache($id);
        if ($cache !== null) {
            $path = $this->savePath . "/" . $id . '.cache';
            $cachePath = realpath($path);
            Logger::info("Get cache: ${cachePath}");

            return $cache["data"];
        }

        return null;
    }

    /**
     * キャッシュのメタデータを取得する
     * @param string キャッシュID
     */
    public function meta($id)
    {
        $cache = $this->cache($id);
        $meta = null;
        if ($cache !== null) {
            $meta = [
                "time" => $cache["time"],
                "ttl" => $cache["ttl"]
            ];
        }

        return $meta;
    }

    /**
     * キャッシュデータを返却する
     * @param string キャッシュID
     */
    private function cache($id)
    {
        $path = $this->savePath . "/" . $id . '.cache';
        $cachePath = realpath($path);
        if ($cachePath !== false && is_file($cachePath)) {
            $data = $this->decode(file_get_contents($cachePath));

            // 制限を確認
            if ($data["ttl"] !== -1) {
                // 期限切れのキャッシュは削除
                if (time() > $data["time"] + $data["ttl"]) {
                    Logger::warn("Expired cache: ${cachePath}");
                    unlink($cachePath);

                    return null;
                }
            }

            return $data;
        }

        return null;
    }

    /**
     * キャッシュを保存する
     * @param string キャッシュID
     * @param object 保存データ
     * @param integer キャッシュ保存時間(デフォルト無期限)
     * @param boolean 上書きするかどうか
     * @return boolean 保存結果
     */
    public function save($id, $data, $ttl = -1, $overwrite = false)
    {
        $content = [
            "time" => time(),
            "ttl" => intval($ttl),
            "data" => $data
        ];

        // キャッシュディレクトリが存在するか
        if (is_dir($this->savePath)) {
            $cachePath = realpath($this->savePath) . '/' . basename($id) . '.cache';
            // キャッシュファイルがない場合またはキャッシュファイルが存在するが、
            // 上書きする場合はキャッシュを新規作成する
            if (!is_file($cachePath) || (is_file($cachePath) && $overwrite === true)) {
                $result = @file_put_contents($cachePath, $this->encode($content), LOCK_EX);
                if ($result !== false) { // ファイルが書き込めた場合
                    Logger::info("Create cache: ${cachePath}");
                    // キャッシュファイルのパーミッションを777にする
                    @chmod($cachePath, 0777);

                    return true;
                } else { // ファイル書き込みに失敗した場合
                    throw new IOException("Can't create cache: " . $cachePath);
                }
            }
        } else {
            Logger::error("Invalid cache directory: " . $this->savePath);
        }

        return false;
    }

    /**
     * キャッシュを削除する
     * @param string キャッシュID
     */
    public function delete($id)
    {
        $cachePath = realpath($this->savePath . "/" . $id . '.cache');
        if ($cachePath) {
            Logger::debug("Cache delete success: ${cachePath}");

            return unlink($cachePath);
        } else {
            Logger::error("Cache delete failure: ${cachePath}");

            return false;
        }
    }
}
