<?php
namespace WebStream\Module\Utility;

use WebStream\Module\Container;
use WebStream\Cache\Driver\ICache;
use WebStream\Cache\Driver\CacheDriverFactory;

/**
 * CacheUtils
 * キャッシュに関するUtility
 * @author Ryuichi Tanaka
 * @since 2016/07/17
 * @version 0.7
 */
trait CacheUtils
{
    use ApplicationUtils;

    /**
     * キャッシュドライバを返却する
     * @param string $cacheName キャッシュドライバ名
     * @return ICache キャッシュドライバ
     */
    public function getCacheDriver(string $cacheName, string $classPrefix = null): ICache
    {
        $driver = null;
        $config = new Container(false);
        $config->classPrefix = $classPrefix;
        $factory = new CacheDriverFactory();

        switch ($cacheName) {
            case "apcu":
                $driver = $factory->create("WebStream\Cache\Driver\Apcu", $config);
                break;
            case "memcached":
                $cacheConfig = \Spyc::YAMLLoad($this->getApplicationRoot() . '/config/cache.yml');
                if (array_key_exists('memcached', $cacheConfig)) {
                    $config->servers = [[$cacheConfig['memcached']['host'], $cacheConfig['memcached']['port']]];
                    $driver = $factory->create("WebStream\Cache\Driver\Memcached", $config);
                }
                break;
            case "redis":
                $cacheConfig = \Spyc::YAMLLoad($this->getApplicationRoot() . '/config/cache.yml');
                if (array_key_exists('redis', $cacheConfig)) {
                    $config->host = $cacheConfig['redis']['host'];
                    $config->port = $cacheConfig['redis']['port'];
                    $driver = $factory->create("WebStream\Cache\Driver\Redis", $config);
                }
                break;
            case "temporaryFile":
                $cacheConfig = \Spyc::YAMLLoad($this->getApplicationRoot() . '/config/cache.yml');
                if (array_key_exists('temporaryfile', $cacheConfig)) {
                    $config->cacheDir = $cacheConfig['temporaryfile']['path'];
                    $driver = $factory->create("WebStream\Cache\Driver\TemporaryFile", $config);
                }
                break;
        }

        return $driver;
    }
}
