<?php
namespace WebStream\Cache\Driver;

use WebStream\DI\Injector;
use WebStream\Module\Container;

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
     * @var \Memcached memcachedオブジェクト
     */
    private $cache;

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
        $this->isAvairable = extension_loaded('memcached');
        $this->cachePrefix = $container->cachePrefix;

        if ($this->isAvairable) {
            $this->cache = new \Memcached();
            $this->cache->addServers($container->servers);

            $defaultOptions = [
                \Memcached::OPT_CONNECT_TIMEOUT => 50,
                \Memcached::OPT_RETRY_TIMEOUT => 50,
                \Memcached::OPT_SEND_TIMEOUT => 50,
                \Memcached::OPT_RECV_TIMEOUT => 50,
                \Memcached::OPT_POLL_TIMEOUT => 50,
                \Memcached::OPT_COMPRESSION => true,
                \Memcached::OPT_LIBKETAMA_COMPATIBLE => true,
                \Memcached::OPT_BINARY_PROTOCOL => true
            ];

            if (\Memcached::HAVE_IGBINARY) {
                $defaultOptions[\Memcached::OPT_SERIALIZER] = \Memcached::SERIALIZER_IGBINARY;
            }

            $this->cache->setOptions($defaultOptions);
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
        $result = null;

        if ($ttl > 0) {
            if ($overwrite) {
                if ($this->cache->replace($key, $value, $ttl) === false) {
                    $result = $this->cache->set($key, $value, $ttl);
                }
            } else {
                $result = $this->cache->set($key, $value, $ttl);
            }
        } else {
            if ($overwrite) {
                if ($this->cache->replace($key, $value) === false) {
                    $result = $this->cache->set($key, $value);
                }
            } else {
                $result = $this->cache->set($key, $value);
            }
        }

        $this->logger->info("Execute cache save: " . $key);
        $this->verifyReturnCode(\Memcached::RES_SUCCESS);

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
        $value = $this->cache->get($key);
        $this->logger->info("Execute cache read: " . $key);

        return $this->verifyReturnCode(\Memcached::RES_SUCCESS) ? $value : null;
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
        $this->cache->delete($key);
        $this->logger->info("Execute cache cleared: " . $key);

        return $this->verifyReturnCode(\Memcached::RES_NOTFOUND);
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): bool
    {
        if (!$this->isAvailableCacheLibrary()) {
            return false;
        }
        $allKeys = $this->cache->getAllKeys();
        if ($allKeys === false) {
            $this->logger->warn("Can't get cache keys: " . $this->cachePrefix . "*");
            $this->cache->flush();

            return true;
        }

        $prefixLength = strlen($this->cachePrefix);
        $targetKeys = [];
        foreach ($allKeys as $key) {
            if (substr($key, 0, $prefixLength)) {
                $targetKeys[] = $key;
            }
        }

        $this->deleteMulti($targetKeys);
        $this->logger->info("Execute all cache cleared: " . $this->cachePrefix . "*");

        return $this->verifyReturnCode(\Memcached::RES_NOTFOUND);
    }

    /**
     * リターンコードを検証する
     * @param int $code 想定コード
     * @return bool 検証結果
     */
    private function verifyReturnCode(int $code)
    {
        if ($code !== $this->cache->getResultCode()) {
            $message = $this->cache->getResultMessage();
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
        if ($this->isAvairable) {
            return true;
        }

        $this->logger->warn("Memcached cache library is unavailable.");

        return false;
    }
}
