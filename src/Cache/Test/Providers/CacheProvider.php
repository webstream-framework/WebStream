<?php
namespace WebStream\Cache\Test\Providers;

use WebStream\Container\Container;
use WebStream\Cache\Driver\Apcu;
use WebStream\Cache\Driver\Memcached;
use WebStream\Cache\Driver\Redis;
use WebStream\Cache\Driver\TemporaryFile;
use WebStream\Cache\Driver\CacheDriverFactory;
use WebStream\IO\File;

/**
 * CacheProvider
 * @author Ryuichi TANAKA.
 * @since 2016/07/11
 * @version 0.7
 */
trait CacheProvider
{
    public function cacheProvider()
    {
        return [
            [$this->getApcuObject()],
            [$this->getMemcachedObject()],
            [$this->getRedisObject()],
            [$this->getTemporaryFileObject()]
        ];
    }

    private function getApcuObject()
    {
        $factory = new CacheDriverFactory();
        $driver = $factory->create("WebStream\Cache\Driver\Apcu");
        $logger = new class() { function __call($name, $args) {} };
        $driver->inject('logger', $logger);

        return $driver;
    }

    private function getMemcachedObject()
    {
        $factory = new CacheDriverFactory();
        $container = new Container(false);
        $container->servers = [["memcached", 11211]];
        $driver = $factory->create("WebStream\Cache\Driver\Memcached", $container);
        $logger = new class() { function __call($name, $args) {} };
        $driver->inject('logger', $logger);

        return $driver;
    }

    private function getRedisObject()
    {
        $factory = new CacheDriverFactory();
        $container = new Container(false);
        $container->host = "redis";
        $container->port = 6379;
        $driver = $factory->create("WebStream\Cache\Driver\Redis", $container);
        $logger = new class() { function __call($name, $args) {} };
        $driver->inject('logger', $logger);

        return $driver;
    }

    private function getTemporaryFileObject()
    {
        $factory = new CacheDriverFactory();
        $container = new Container(false);
        $container->cacheDir = "/tmp";
        $driver = $factory->create("WebStream\Cache\Driver\TemporaryFile", $container);
        $logger = new class() { function __call($name, $args) {} };
        $driver->inject('logger', $logger);

        return $driver;
    }
}
