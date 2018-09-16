<?php
namespace WebStream\Cache\Driver;

use WebStream\Container\Container;

/**
 * ICache
 * @author Ryuichi TANAKA.
 * @since 2015/07/05
 * @version 0.7
 */
interface ICache
{
    /**
     * Constructor
     * @param Container $container 依存コンテナ
     */
    public function __construct(Container $container);

    /**
     * キャッシュを登録する
     * @param mixed $key キャッシュキー
     * @param mixed $value キャッシュ値
     * @param int $ttl キャッシュ保持期間(秒)
     * @param bool $overwrite 上書きフラグ
     */
    public function add($key, $value, $ttl, $overwrite): bool;

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
