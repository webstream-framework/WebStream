<?php
namespace WebStream\Cache\Driver;

use WebStream\DI\Injector;
use WebStream\Container\Container;

/**
 * Memcached
 * @author Ryuichi Tanaka
 * @since 2015/07/08
 * @version 0.7
 */
class Memcached implements ICache
{
    use Injector;

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
        $this->cachePrefix = $this->cacheContainer->cachePrefix . '.' . $this->cacheContainer->classPrefix . '.';
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
        $result = null;

        if ($ttl > 0) {
            if ($overwrite) {
                $result = $this->cacheContainer->driver->replace($key, $value, $ttl);
                if ($result === false) {
                    $result = $this->cacheContainer->driver->set($key, $value, $ttl);
                }
            } else {
                $result = $this->cacheContainer->driver->set($key, $value, $ttl);
            }
        } else {
            if ($overwrite) {
                $result = $this->cacheContainer->driver->replace($key, $value);
                if ($result === false) {
                    $result = $this->cacheContainer->driver->set($key, $value);
                }
            } else {
                $result = $this->cacheContainer->driver->set($key, $value);
            }
        }

        $this->logger->info("Execute cache save: " . $key);
        $this->verifyReturnCode($this->cacheContainer->codes['success']);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        if (!$this->isAvailableCacheLibrary()) {
            return false;
        }
        $key = $this->cachePrefix . $key;
        $result = $this->cacheContainer->driver->get($key);

        if ($result !== false) {
            $this->logger->info("Execute cache read: " . $key);
        } else {
            $this->logger->warn("Failed to read cache: " . $key);
        }

        return $this->verifyReturnCode($this->cacheContainer->codes['success']) ? $result : null;
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
        $this->cacheContainer->driver->delete($key);

        if ($this->verifyReturnCode($this->cacheContainer->codes['notfound'])) {
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
        $allKeys = $this->cacheContainer->driver->getAllKeys();
        if ($allKeys === false) {
            $this->logger->warn("Can't get cache keys: " . $this->cachePrefix . "*");
            $this->cacheContainer->driver->flush();

            return true;
        }

        $prefixLength = strlen($this->cachePrefix);
        $targetKeys = [];
        foreach ($allKeys as $key) {
            if (substr($key, 0, $prefixLength)) {
                $targetKeys[] = $key;
            }
        }

        $this->cacheContainer->driver->deleteMulti($targetKeys);

        if ($this->verifyReturnCode($this->cacheContainer->codes['notfound'])) {
            $this->logger->info("Execute all cache cleared: " . $this->cachePrefix . "*");
            return true;
        } else {
            $this->logger->warn("Failed to clear all cache: " . $this->cachePrefix . "*");
            return false;
        }
    }

    /**
     * リターンコードを検証する
     * @param int $code 想定コード
     * @return bool 検証結果
     */
    private function verifyReturnCode(int $code): bool
    {
        if ($code !== $this->cacheContainer->driver->getResultCode()) {
            $message = $this->cacheContainer->driver->getResultMessage();
            $this->logger->warn("Error $code interacting with memcached: $message");

            return false;
        }

        return true;
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

        $this->logger->warn("Memcached cache library is unavailable.");

        return false;
    }
}
