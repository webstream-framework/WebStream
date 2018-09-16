<?php
namespace WebStream\Log\Outputter;

/**
 * ConsoleOutputter
 * @author Ryuichi Tanaka
 * @since 2016/01/26
 * @version 0.7
 */
class ConsoleOutputter implements IOutputter, ILazyWriter
{
    /**
     * https://github.com/php/php-src/tree/master/sapi
     * PHP7以前のものは対応しない
     * @var SAPIリスト
     */
    private $sapis = [
        'apache2handler' => 'http',
        'cgi'            => 'http',
        'cli'            => 'console',
        'fpm'            => 'http',
        'embed'          => 'unsupported',
        'litespeed'      => 'unsupported',
        'phpdbg'         => 'unsupported',
        'tests'          => 'unsupported'
    ];

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
     * constructor
     */
    public function __construct($bufferSize = 1000)
    {
        $this->logMessages = [];
        $this->bufferSize = $bufferSize;
        $this->isLazyWrite = false;
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->writeLog(implode("", $this->logMessages));
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
        $this->flush();
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
        $sapi = php_sapi_name();
        if (array_key_exists($sapi, $this->sapis) && $this->sapis[$sapi] === 'console') {
            echo $message;
        }
    }
}
