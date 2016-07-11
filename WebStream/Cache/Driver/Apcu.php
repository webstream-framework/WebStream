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
     * @var Container キャッシュ依存コンテナ
     */
    private $cacheContainer;

    /**
     * {@inheritdoc}
     */
    public function __construct(Container $cacheContainer)
    {
        $this->cacheContainer = $cacheContainer;
    }

    /**
     * {@inheritdoc}
     */
    public function add($key, $value, $ttl = 0, $overrite = false): bool
    {
        if (!$this->isAvailableCacheLibrary()) {
            return false;
        }
        $key = $this->cacheContainer->cachePrefix . $key;

        $result = $overrite ? $this->cacheContainer->driver->delegate("apcu_store", [$key, $value, $ttl]) :
            $this->cacheContainer->driver->delegate("apcu_add", [$key, $value, $ttl]);
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
        $key = $this->cacheContainer->cachePrefix . $key;
        $value = $this->cacheContainer->driver->delegate("apcu_fetch", [$key]);

        if ($value !== false) {
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
        $key = $this->cacheContainer->cachePrefix . $key;

        if ($this->cacheContainer->driver->delegate("apcu_delete", [$key])) {
            $this->logger->info("Execute cache cleared: " . $key);
            return true;
        } else {
            $this->logger->warn("Failed to clear cache: " . $key);
            return false;
        }
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
            $obj = new \APCUIterator('/^' . $this->cacheContainer->cachePrefix . '/', APC_ITER_KEY);
            if ($this->cacheContainer->driver->delegate("apcu_delete", [$obj])) {
                $this->logger->info("Execute all cache cleared: " . $this->cacheContainer->cachePrefix . "*");
                return true;
            }
        } elseif ($this->cacheContainer->driver->delegate("apcu_clear_cache")) {
            $this->logger->info("Execute all cache cleared: " . $this->cacheContainer->cachePrefix . "*");
            return true;
        }

        $this->logger->warn("Failed to clear all cache: " . $this->cacheContainer->cachePrefix . "*");
        return false;
    }

    /**
     * キャッシュライブラリが使用可能か検査する
     * @return bool 使用可能でtrue
     */
    private function isAvailableCacheLibrary(): bool
    {
        if ($this->cacheContainer->available) {
            return true;
        }

        $this->logger->warn("APCu cache library is unavailable.");

        return false;
    }
}
