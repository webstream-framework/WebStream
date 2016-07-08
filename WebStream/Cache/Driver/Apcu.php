<?php
namespace WebStream\Cache\Driver;

use WebStream\DI\Injector;
use WebStream\Module\Container;

/**
 * Apcu
 * TTLがリクエストによりずれる問題はAPCuの仕様なので対応しない
 * @author Ryuichi Tanaka
 * @since 2015/07/05
 * @version 0.7
 */
class Apcu implements ICache
{
    use injector;

    /**
     * @var bool キャッシュ使用可能フラグ
     */
    private $isAvairable;

    /**
     * @var string キャッシュ接頭辞
     */
    private $cachePrefix;

    /**
     * constructor
     */
    public function __construct(Container $container)
    {
        $this->isAvairable = extension_loaded('apcu');
        $this->cachePrefix = $container->cachePrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function add($key, $value, $ttl = 0, $overrite = false): bool
    {
        if (!$this->isAvailableCacheLibrary()) {
            return false;
        }
        $key = $this->cachePrefix . $key;

        $result = $overrite ? apcu_store($key, $value, $ttl) : apcu_add($key, $value, $ttl);
        $this->logger->info("Execute cache save: " . $key);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        if (!$this->isAvailableCacheLibrary()) {
            return null;
        }
        $key = $this->cachePrefix . $key;
        $value = apcu_fetch($key, $isSuccess);

        if ($isSuccess) {
            $this->logger->info("Execute cache read: " . $key);
        } else {
            $this->logger->warn("Failed to read cache: " . $key);
            $value = null;
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key): bool
    {
        if (!$this->isAvailableCacheLibrary()) {
            return false;
        }
        $key = $this->cachePrefix . $key;

        return apcu_delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): bool
    {
        if (!$this->isAvailableCacheLibrary()) {
            return false;
        }

        if (class_exists('\APCUIterator')) {
            return apcu_delete(new \APCUIterator('/^' . $this->cachePrefix . '/', APC_ITER_KEY));
        } else {
            return apcu_clear_cache();
        }
    }

    /**
     * キャッシュライブラリが使用可能か検査する
     * @return bool 使用可能でtrue
     */
    private function isAvailableCacheLibrary(): bool
    {
        if ($this->isAvairable) {
            return true;
        }

        $this->logger->warn("APCu cache library is unavailable.");

        return false;
    }
}
