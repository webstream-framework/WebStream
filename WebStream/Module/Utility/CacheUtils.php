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
    public function getCacheDriver(string $cacheName): ICache
    {
        $driver = null;
        $factory = new CacheDriverFactory();

        switch ($cacheName) {
            case "apcu":
                $driver = $factory->create("WebStream\Cache\Driver\Apcu");
                break;
            case "memcached":
                $cacheConfig = \Spyc::YAMLLoad($this->getApplicationRoot() . '/config/cache.yml');
                if (array_key_exists('memcached', $cacheConfig)) {
                    $config = new Container(false);
                    $config->servers = [$cacheConfig['host'], $cacheConfig['port']];
                    $driver = $factory->create("WebStream\Cache\Driver\Memcached", $config);
                }
                break;
            case "redis":
                $cacheConfig = \Spyc::YAMLLoad($this->getApplicationRoot() . '/config/cache.yml');
                if (array_key_exists('redis', $cacheConfig)) {
                    $config = new Container(false);
                    $config->host = $cacheConfig['host'];
                    $config->port = $cacheConfig['port'];
                    $driver = $factory->create("WebStream\Cache\Driver\Redis", $config);
                }
                break;
            case "temporaryFile":
                $config = new Container();
                $config->cacheDir = "/tmp";
                $driver = $factory->create("WebStream\Cache\Driver\TemporaryFile", $config);
                break;
        }

        return $driver;
    }
}
