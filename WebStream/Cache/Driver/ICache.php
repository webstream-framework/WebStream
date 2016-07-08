<?php
namespace WebStream\Cache\Driver;

/**
 * ICache
 * @author Ryuichi TANAKA.
 * @since 2015/07/05
 * @version 0.7
 */
interface ICache
{
    /**
     * キャッシュを登録する
     * @param mixed $key キャッシュキー
     * @param mixed $value キャッシュ値
     * @param int $ttl キャッシュ保持期間(秒)
     * @param bool $overrite 上書きフラグ
     */
    public function add($key, $value, $ttl, $overrite): bool;

    /**
     * キャッシュを取得する
     * @param mixed $key キャッシュキー
     * @return mixed キャッシュ値
     */
    public function get($key);

    /**
     * キャッシュを削除する
     * @param mixed $key キャッシュキー
     * @return bool キャッシュ削除結果
     */
    public function delete($key): bool;

    /**
     * キャッシュをすべて削除する
     * @return bool キャッシュ削除結果
     */
    public function clear(): bool;
}
