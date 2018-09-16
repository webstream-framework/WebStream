<?php
namespace WebStream\Cache\Driver;

use WebStream\DI\Injector;
use WebStream\Container\Container;
use WebStream\IO\File;
use WebStream\Exception\Extend\IOException;

/**
 * TemporaryFile
 * @author Ryuichi Tanaka
 * @since 2015/07/12
 * @version 0.7
 */
class TemporaryFile implements ICache
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
        $this->cachePrefix = $this->cacheContainer->cachePrefix . '.' . $this->cacheContainer->classPrefix . '.';
    }

    /**
     * {@inheritdoc}
     */
    public function add($key, $data, $ttl = 0, $overwrite = false): bool
    {
        if (!$this->isAvailableCacheLibrary()) {
            return false;
        }
        $key = $this->cachePrefix . $key;
        $writer = null;
        $isAppend = !$overwrite;

        $value = base64_encode(serialize([
            "time" => time(),
            "ttl" => intval($ttl),
            "data" => $data
        ]));

        try {
            $file = new File($this->cacheContainer->cacheDir . '/' . $key . '.cache');
            $writer = $this->cacheContainer->ioContainer->fileWriter->getWriter($file, $isAppend);
            $writer->write($value);
            $this->logger->info("Execute cache save: " . $key);
            return true;
        } catch (IOException $e) {
            $this->logger->warn($e->getMessage());
            return false;
        } finally {
            if ($writer !== null) {
                try {
                    $writer->close();
                } catch (IOException $ignore) {
                    // Nothing to do
                }
            }
        }
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
        $value = null;
        $reader = null;
        $file = new File($this->cacheContainer->cacheDir . '/' . $key . '.cache');

        if ($file->isReadable()) {
            try {
                $reader = $this->cacheContainer->ioContainer->fileReader->getReader($file);
                $data = unserialize(base64_decode($reader->read()));
                if ($data['ttl'] > 0) {
                    if (time() > $data['time'] + $data['ttl']) {
                        $this->logger->info("Expired cache: " . $key);
                        $file->delete();
                    }
                } else {
                    $value = $data['data'];
                    $this->logger->info("Execute cache read: " . $key);
                }
            } catch (IOException $e) {
                $this->logger->warn($e->getMessage());
            } finally {
                if ($reader !== null) {
                    try {
                        $reader->close();
                    } catch (IOException $ignore) {
                        // Nothing to do
                    }
                }
            }
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
        $result = false;

        $file = new File($this->cacheContainer->cacheDir . '/' . $key . '.cache');
        if ($file->isWritable()) {
            $result = $file->delete();
            if ($result) {
                $this->logger->info("Execute cache cleared: " . $key);
            } else {
                $this->logger->warn("Failed to clear cache: " . $key);
            }
        } else {
            $this->logger->warn("Failed to clear cache: " . $key);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): bool
    {
        if (!$this->isAvailableCacheLibrary()) {
            return false;
        }
        $dir = new File($this->cacheContainer->cacheDir);
        $result = true;

        if ($dir->isReadable()) {
            $iterator = $this->cacheContainer->ioContainer->fileIterator->getIterator($dir->getFilePath());
            foreach ($iterator as $filepath => $fileObject) {
                if (strpos($filepath, $this->cachePrefix, 0) !== false) {
                    try {
                        $file = new File($filepath);
                        if ($file->isWritable()) {
                            if ($file->delete()) {
                                $this->logger->info("Execute cache cleared: " . $this->cachePrefix . "*");
                            } else {
                                $this->logger->warn("Failed to clear cache: " . $this->cachePrefix . "*");
                                $result = false;
                            }
                        } else {
                            $result = false;
                        }
                    } catch (IOException $e) {
                        $this->logger->warn($e->getMessage());
                        $result = false;
                    }
                }
            }
        } else {
            $this->logger->warn("Can't read directory:" . $dir->getFilePath());
            $result = false;
        }

        return $result;
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

        $this->logger->warn("TemporaryFile cache library is unavailable.");

        return false;
    }
}
