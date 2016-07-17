<?php
namespace WebStream\Log\Outputter;

use WebStream\IO\Writer\SimpleFileWriter;
use WebStream\Cache\LoggerCache;
use WebStream\Cache\Driver\ICache;
use WebStream\Module\Utility\LoggerUtils;
use WebStream\Module\Utility\CacheUtils;

/**
 * FileOutputter
 * @author Ryuichi Tanaka
 * @since 2016/01/26
 * @version 0.7
 */
class FileOutputter implements IOutputter, ILazyWriter
{
    use LoggerUtils, CacheUtils;

    /**
     * @var string ログファイルパス
     */
    private $logPath;

    /**
     * @var ICache キャッシュドライバ
     */
    private $driver;

    /**
     * @var int バッファリングサイズ
     */
    private $bufferSize;

    /**
     * @var bool 遅延書き出しフラグ
     */
    private $isLazyWrite;

    /**
     * @var SimpleFileWriter Writerオブジェクト
     */
    private $writer;

    /**
     * @var LoggerCache ログキャッシュ
     */
    private $cache;

    /**
     * constructor
     * @param string $logPath ログファイルパス
     * @param int $bufferSize バッファリングサイズ
     */
    public function __construct(string $logPath, int $bufferSize = 1000)
    {
        $this->logPath = $logPath;
        $driver = $this->getCacheDriver("apcu");
        // LoggerCacheの中ではログは取らない
        $driver->inject('logger', new class() { function __call($name, $args) {} });
        $this->driver = $driver;
        $this->bufferSize = $bufferSize;
        $this->enableLazyWrite();
        $this->writer = new SimpleFileWriter($logPath);
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        try {
            if ($this->isLazyWrite) {
                $this->writeLog(implode("", $this->cache->get()));
            }
        } catch (\Exception $ignore) {
            // デストラクタで例外が発生すると致命的なエラーとなる
        }
    }

    /**
     * {@inheritdoc}
     */
    public function enableLazyWrite()
    {
        if ($this->driver !== null) {
            $this->isLazyWrite = true;
            $this->cache = new LoggerCache($this->driver);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function enableDirectWrite()
    {
        if ($this->isLazyWrite && $this->cache->length() > 0) {
            $this->writeLog(implode("", $this->cache->get()));
        }

        $this->isLazyWrite = false;
        $this->cache = null;
    }

    /**
     * {@inheritdoc}
     */
    public function write($message)
    {
        if ($this->isLazyWrite) {
            if ($this->cache->length() >= $this->bufferSize) {
                $this->flush();
                $this->clear();
            }
            $this->cache->add($message);
        } else {
            $this->writeLog($message);
        }
    }

    /**
     * バッファをログ出力する
     */
    private function flush()
    {
        if ($this->isLazyWrite && $this->cache->length() > 0) {
            $this->writeLog(implode("", $this->cache->get()));
        }
    }

    /**
     * ログファイルに書き出す
     * @param string $message ログメッセージ
     */
    private function writeLog($message)
    {
        $this->writer->write($message);
    }
}
