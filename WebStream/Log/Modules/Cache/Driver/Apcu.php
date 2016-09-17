<?php
namespace WebStream\Cache\Driver;

use WebStream\DI\Injector;
use WebStream\Container\Container;

/**
 * Apcu
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
     * @var string キャッシュ接頭辞
     */
    private $cachePrefix;

    /**
     * {@inheritdoc}
     */
    public function __construct(Container $cacheContainer)
    {
        $this->cacheContainer = $cacheContainer;
        $this->cachePrefix = $this->cacheContainer->cachePrefix . '.';
        if (!empty($this->cacheContainer->classPrefix)) {
            $this->cachePrefix .= $this->cacheContainer->classPrefix . '.';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function add($key, $value, $ttl = 0, $overwrite = false): bool
    {
        if (!$this->isAvailableCacheLibrary()) {
            return false;
        }
        $key = $this->cachePrefix . $key;

        $result = $overwrite ? $this->cacheContainer->driver->delegate("apcu_store", [$key, $value, $ttl]) :
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
        $key = $this->cachePrefix . $key;
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
        $key = $this->cachePrefix . $key;

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
            $obj = new \APCUIterator('/^' . $this->cachePrefix . '/', APC_ITER_KEY);
            if ($this->cacheContainer->driver->delegate("apcu_delete", [$obj])) {
                $this->logger->info("Execute all cache cleared: " . $this->cachePrefix . "*");
                return true;
            }
        } elseif ($this->cacheContainer->driver->delegate("apcu_clear_cache")) {
            $this->logger->info("Execute all cache cleared: " . $this->cachePrefix . "*");
            return true;
        }

        $this->logger->warn("Failed to clear all cache: " . $this->cachePrefix . "*");
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
