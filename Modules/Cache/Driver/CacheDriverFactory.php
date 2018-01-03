<?php
namespace WebStream\Cache\Driver;

use WebStream\Container\Container;
use WebStream\IO\File;
use WebStream\IO\Reader\FileReader;
use WebStream\IO\Writer\FileWriter;

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

        if ($config === null) {
            $config = new Container(false);
        }

        switch ($classpath) {
            case "WebStream\Cache\Driver\Apcu":
                $cache = new $classpath($this->getApcuContainer($config));
                break;
            case "WebStream\Cache\Driver\Memcached":
                $cache = new $classpath($this->getMemcachedContainer($config));
                break;
            case "WebStream\Cache\Driver\Redis":
                $cache = new $classpath($this->getRedisContainer($config));
                break;
            case "WebStream\Cache\Driver\TemporaryFile":
                $cache = new $classpath($this->getTemporaryFileContainer($config));
                break;
        }

        return $cache;
    }

    /**
     * APCuオブジェクトを返却する
     * @param Container $container 依存コンテナ
     * @return Container キャッシュ依存コンテナ
     */
    private function getApcuContainer(Container $container): Container
    {
        $cacheContainer = new Container();
        $cacheContainer->available = extension_loaded('apcu');
        $cacheContainer->cachePrefix = "cache.apcu";
        $cacheContainer->classPrefix = $container->classPrefix ?: "";
        $cacheContainer->driver = new class()
        {
            public function delegate($function, array $args = [])
            {
                return function_exists($function) ? call_user_func_array($function, $args) : null;
            }
        };

        return $cacheContainer;
    }

    /**
     * Memcachedオブジェクトを返却する
     * @param Container $container 依存コンテナ
     * @return Container キャッシュ依存コンテナ
     */
    private function getMemcachedContainer(Container $container): Container
    {
        $cacheContainer = new Container();
        $cacheContainer->driver = new \Memcached();
        $cacheContainer->available = extension_loaded('memcached');
        $cacheContainer->cachePrefix = "cache.memcached";
        $cacheContainer->classPrefix = $container->classPrefix ?: "";
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

        return $cacheContainer;
    }

    /**
     * Redisオブジェクトを返却する
     * @param Container $container 依存コンテナ
     * @return Container キャッシュ依存コンテナ
     */
    private function getRedisContainer(Container $container): Container
    {
        $cacheContainer = new Container();
        $cacheContainer->driver = new \Redis();
        $cacheContainer->available = extension_loaded('redis');
        $cacheContainer->cachePrefix = "cache.redis";
        $cacheContainer->classPrefix = $container->classPrefix ?: "";
        $cacheContainer->redisOptPrefix = \Redis::OPT_PREFIX;

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

                $cacheContainer->driver->setOption(\Redis::OPT_PREFIX, $container->cachePrefix . '.' . $container->classPrefix);
                $cacheContainer->driver->setOption(\Redis::OPT_SCAN, \Redis::SCAN_RETRY);
            }
        }

        return $cacheContainer;
    }

    /**
     * TemporaryFileオブジェクトを返却する
     * @param Container $container 依存コンテナ
     * @return Container キャッシュ依存コンテナ
     */
    private function getTemporaryFileContainer(Container $container): Container
    {
        $cacheContainer = new Container();
        $cacheContainer->cachePrefix = "cache.file";
        $cacheContainer->classPrefix = $container->classPrefix ?: "";
        $dir = new File($container->cacheDir);
        $cacheContainer->available = $dir->isWritable();
        $cacheContainer->cacheDir = $container->cacheDir;
        $cacheContainer->ioContainer = new Container();
        $cacheContainer->ioContainer->fileReader = new class()
        {
            public function getReader(File $file)
            {
                return new FileReader($file);
            }
        };
        $cacheContainer->ioContainer->fileWriter = new class()
        {
            public function getWriter($file, $isAppend)
            {
                return new FileWriter($file, $isAppend);
            }
        };
        $cacheContainer->ioContainer->fileIterator = new class()
        {
            public function getIterator($dirPath)
            {
                $file = new File($dirPath);
                $iterator = [];
                if ($file->isDirectory()) {
                    $iterator = new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($file->getFilePath()),
                        \RecursiveIteratorIterator::LEAVES_ONLY,
                        \RecursiveIteratorIterator::CATCH_GET_CHILD // for Permission deny
                    );
                }

                return $iterator;
            }
        };

        return $cacheContainer;
    }
}
