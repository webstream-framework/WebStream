<?php
/**
 * Cacheクラス
 * @author Ryuichi Tanaka
 * @since 2011/10/08
 */
class Cache {
    const CACHE_DIR_WIN = "C:\\tmp\\";
    const CACHE_DIR_UNIX = "/tmp/";

    /** キャッシュの保存ディレクトリ */
    private $save_path;
    
    /**
     * コンストラクタ
     * @param String キャッシュの保存ディレクトリパス
     */
    public function __construct($save_path = null) {
        $this->save_path = $save_path;
        if ($this->save_path === null) {
            if (PHP_OS === "WIN32" || PHP_OS === "WINNT") {
                $this->save_path = self::CACHE_DIR_WIN;
            }
            else {
                $this->save_path = self::CACHE_DIR_UNIX;
            }
        }
    }

    /**
     * キャッシュを取得する
     * @param String キャッシュID
     * @return String キャッシュデータ
     */
    public function get($id) {
        $cache = $this->cache($id);
        if ($cache !== null) {
            $path = $this->save_path . $id . '.cache';
            $cache_path = realpath($path);
            Logger::info("Get cache: ${cache_path}");
            return $cache["data"];
        }
        return null;
    }

    /**
     * キャッシュのメタデータを取得する
     * @param String キャッシュID
     */
    public function meta($id) {
        $cache = $this->cache($id);
        $meta = null;
        if ($cache !== null) {
            $meta = array(
                "time" => $cache["time"],
                "ttl" => $cache["ttl"]
            );
        }
        return $meta;
    }
    
    /**
     * キャッシュデータを返却する
     * @param String キャッシュID
     */
    private function cache($id) {
        $path = $this->save_path . $id . '.cache';
        $cache_path = realpath($path);
        if ($cache_path !== false && is_file($cache_path)) {
            $data = unserialize(file_get_contents($cache_path));
            // 期限切れのキャッシュは削除
            if (time() > $data["time"] + $data["ttl"]) {
                Logger::warn("Expired cache: ${cache_path}");
                unlink($cache_path);
                return null;
            }
            return $data;
        }
        else {
            Logger::error("Can't get cache: ${path}");
        }
        
        return null;
    }
    
    /**
     * キャッシュを保存する
     * @param String キャッシュID
     * @param Object 保存データ
     * @param Integer キャッシュ保存時間
     * @param Boolean 上書きするかどうか
     */
    public function save($id, $data, $ttl = 60, $overwrite = false) {
        $content = array(
            "time" => time(),
            "ttl" => intval($ttl),
            "data" => $data
        );
        
        // キャッシュディレクトリが存在するか
        if (is_dir($this->save_path)) {
            $cache_path = realpath($this->save_path) . '/' . basename($id) . '.cache';
            // キャッシュファイルがない場合またはキャッシュファイルが存在するが、
            // 上書きする場合はキャッシュを新規作成する
            if (!is_file($cache_path) || (is_file($cache_path) && $overwrite === true)) {
                try {
                    $result = file_put_contents($cache_path, serialize($content));
                    // ファイルが書き込めた場合
                    if ($result !== false) {
                        Logger::info("Create cache: ${cache_path}");
                        // キャッシュファイルのパーミッションを777にする
                        @chmod($cache_path, 0777);
                        return true;
                    }
                    Logger::error("Can't create cache: ${cache_path}");
                }
                catch (Exception $e) {
                    Logger::error($e->getMessage(), $e->getTraceAsString());
                }
            }
        }
        else {
            Logger::error("Invalid cache directory: " . $this->save_path);
        }
        return false;
    }

    /**
     * キャッシュを削除する
     * @param String キャッシュID
     */
    public function delete($id) {
        $path = $this->save_path . $id . '.cache';
        $cache_path = realpath($this->save_path . $id . '.cache');
        if ($cache_path) {
            Logger::debug("Cache delete success: ${cache_path}");
            return unlink($cache_path);
        }
        else {
            Logger::error("Cache delete failure: ${path}");
            return false;
        }
    }
}