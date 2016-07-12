<?php
namespace WebStream\Cache\Driver;

use WebStream\Module\Container;

/**
 * CacheDriverFactory
 * @author Ryuichi Tanaka
 * @since 2015/07/10
 * @version 0.7
 */
class CacheDriverFactory
{
    /**
     * キャッシュドライバオブジェクトを作成する
     * @param string $classpath ドライバクラスパス
     * @param Container $config 依存コンテナ
     * @return ICache キャッシュドライバオブジェクト
     */
    public function create(string $classpath, Container $config = null): ICache
    {
        $cache = null;
        switch ($classpath) {
            case "WebStream\Cache\Driver\Apcu":
                $cache = $this->createApcu();
                break;
            case "WebStream\Cache\Driver\Memcached":
                $cache = $this->createMemcached($config);
                break;
            case "WebStream\Cache\Driver\Redis":
                $cache = $this->createRedis($config);
                break;
        }

        return $cache;
    }

    /**
     * APCuオブジェクトを返却する
     * @return ICache キャッシュオブジェクト
     */
    private function createApcu(): ICache
    {
        $cacheContainer = new Container();
        $cacheContainer->available = extension_loaded('apcu');
        $cacheContainer->cachePrefix = "cache.apcu.";
        $cacheContainer->driver = new class()
        {
            public function delegate($function, array $args = [])
            {
                return function_exists($function) ? call_user_func_array($function, $args) : null;
            }
        };

        return new Apcu($cacheContainer);
    }

    /**
     * Memcachedオブジェクトを返却する
     * @param Container $container 依存コンテナ
     * @return ICache キャッシュオブジェクト
     */
    private function createMemcached(Container $container): ICache
    {
        $cacheContainer = new Container();
        $cacheContainer->driver = new \Memcached();
        $cacheContainer->available = extension_loaded('memcached');
        $cacheContainer->cachePrefix = "cache.memcached.";
        $cacheContainer->codes = [
            'success' => \Memcached::RES_SUCCESS,
            'notfound' => \Memcached::RES_NOTFOUND
        ];

        if ($cacheContainer->available) {
            $cacheContainer->driver->addServers($container->servers);

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

            $cacheContainer->driver->setOptions($defaultOptions);
        }

        return new Memcached($cacheContainer);
    }

    /**
     * Redisオブジェクトを返却する
     * @param Container $container 依存コンテナ
     * @return ICache キャッシュオブジェクト
     */
    private function createRedis(Container $container): ICache
    {
        $cacheContainer = new Container();
        $cacheContainer->driver = new \Redis();
        $cacheContainer->available = extension_loaded('redis');
        $cacheContainer->cachePrefix = "cache.redis.";

        if ($cacheContainer->available) {
            $host = $container->host;
            $port = $container->port;
            $socket = $container->socket;
            $password = $container->password;
            $isAuthed = true;

            if ($password !== null) {
                $isAuthed = $cacheContainer->driver->auth($password);
            }

            if ($isAuthed) {
                if ($host !== null && $port !== null) {
                    $cacheContainer->available = $cacheContainer->driver->connect($host, $port);
                } elseif ($socket !== null) {
                    $cacheContainer->available = $cacheContainer->driver->connect($socket);
                }

                if (defined('\Redis::SERIALIZER_IGBINARY')) {
                    $cacheContainer->driver->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_IGBINARY);
                }

                $cacheContainer->driver->setOption(\Redis::OPT_PREFIX, $container->cachePrefix);
                $cacheContainer->driver->setOption(\Redis::OPT_SCAN, \Redis::SCAN_RETRY);
            }
        }

        return new Redis($cacheContainer);
    }
}
