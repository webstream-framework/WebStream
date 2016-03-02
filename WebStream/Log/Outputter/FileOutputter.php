<?php
namespace WebStream\Log\Outputter;

use WebStream\IO\Writer\SimpleFileWriter;

/**
 * FileOutputter
 * @author Ryuichi Tanaka
 * @since 2016/01/26
 * @version 0.7
 */
class FileOutputter implements IOutputter, ILazyWriter
{
    /**
     * @var string ログファイルパス
     */
    private $logPath;

    /**
     * @var array<string> ログメッセージリスト
     */
    private $logMessages;

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
     * constructor
     * @param string $logPath ログファイルパス
     * @param int $bufferSize バッファリングサイズ
     */
    public function __construct($logPath, $bufferSize = 1000)
    {
        $this->logPath = $logPath;
        $this->logMessages = [];
        $this->bufferSize = $bufferSize;
        $this->isLazyWrite = true;
        $this->writer = new SimpleFileWriter($logPath);
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        try {
            $this->writeLog(implode("", $this->logMessages));
        } catch (\Exception $ignore) {
            // デストラクタで例外が発生すると致命的なエラーとなる
        }
    }

    /**
     * {@inheritdoc}
     */
    public function enableLazyWrite()
    {
        $this->isLazyWrite = true;
    }

    /**
     * {@inheritdoc}
     */
    public function enableDirectWrite()
    {
        if (count($this->logMessages) > 0) {
            $this->writeLog(implode("", $this->logMessages));
            $this->logMessages = [];
        }

        $this->isLazyWrite = false;
    }

    /**
     * {@inheritdoc}
     */
    public function write($message)
    {
        if ($this->isLazyWrite) {
            if (count($this->logMessages) >= $this->bufferSize) {
                $this->flush();
                $this->clear();
            }
            $this->logMessages[] = $message;
        } else {
            $this->writeLog($message);
        }
    }

    /**
     * バッファをクリアする
     */
    private function clear()
    {
        $this->logMessages = [];
    }

    /**
     * バッファをログ出力する
     */
    private function flush()
    {
        if ($this->isLazyWrite && count($this->logMessages) > 0) {
            $this->writeLog(implode("", $this->logMessages));
            $this->clear();
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
