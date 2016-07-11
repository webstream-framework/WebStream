<?php
namespace WebStream\Cache\Driver;

use WebStream\DI\Injector;
use WebStream\Module\Container;

/**
 * Redis
 * @author Ryuichi Tanaka
 * @since 2015/07/09
 * @version 0.7
 */
class Redis implements ICache
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

        if (is_array($value)) {
            $value = json_encode($value);
        }

        $result = false;
        if ($ttl > 0) {
            if ($overrite) {
                $result = $this->cacheContainer->driver->setEx($key, $ttl, $value);
            } else {
                $result = $this->cacheContainer->driver->set($key, $value, ['nx', 'ex' => $ttl]);
            }
        } else {
            if ($overrite) {
                $result = $this->cacheContainer->driver->set($key, $value);
            } else {
                $result = $this->cacheContainer->driver->setNx($key, $value);
            }
        }

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

        $result = $this->cacheContainer->driver->get($key);
        $this->logger->info("Execute cache read: " . $key);

        if ($result !== false) {
            $this->logger->info("Execute cache read: " . $key);
        } else {
            $this->logger->warn("Failed to read cache: " . $key);
            $result = null;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key): bool
    {
        if (!$this->isAvailableCacheLibrary()) {
            return false;
        }

        if ($this->cacheContainer->driver->delete($key)) {
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

        $it = null;
        $result = 1;
        $this->cacheContainer->driver->setOption(\Redis::OPT_PREFIX, "");
        while ($keys = $this->cacheContainer->driver->scan($it, "*")) {
            $result *= $this->cacheContainer->driver->delete($keys);
        }
        $this->cacheContainer->driver->setOption(\Redis::OPT_PREFIX, $this->cacheContainer->cachePrefix);

        if ($result > 0) {
            $this->logger->info("Execute all cache cleared: " . $this->cacheContainer->cachePrefix . "*");
            return true;
        } else {
            $this->logger->warn("Failed to clear all cache: " . $this->cacheContainer->cachePrefix . "*");
            return false;
        }
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

        $this->logger->warn("Redis cache library is unavailable.");

        return false;
    }
}
