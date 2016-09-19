<?php
namespace WebStream\Log;

use WebStream\IO\File;
use WebStream\IO\Reader\FileReader;
use WebStream\IO\Writer\SimpleFileWriter;
use WebStream\Container\Container;
use WebStream\Exception\Extend\IOException;
use WebStream\Exception\Extend\LoggerException;

/**
 * Loggerクラス
 * @author Ryuichi Tanaka
 * @since 2012/01/16
 * @version 0.7
 */
class Logger
{
    use LoggerUtils;

    /**
     * @var Logger ロガー
     */
    private static $logger;

    /**
     * @var LoggerFormatter ロガーフォーマッタ
     */
    private static $formatter;

    /**
     * @var Container ログ設定コンテナ
     */
    private static $config;

    /**
     * @var Container ログ設定コンテナ
     */
    private $logConfig;

    /**
     * @var array<IOutputter> Outputterリスト
     */
    private $outputters;

    /**
     * @var Container IOコンテナ
     */
    private $ioContainer;

    /**
     * @var File ログファイル
     */
    private $logFile;

    /**
     * @var File ステータスファイル
     */
    private $statusFile;

    /**
     * コンストラクタ
     * @param Container ログ設定コンテナ
     */
    private function __construct(Container $logConfig)
    {
        $this->logConfig = $logConfig;
        $this->outputters = [];

        $logFile = new File($logConfig->logPath);
        $statusFile = new File($logConfig->statusPath);

        $this->ioContainer = new Container();

        $this->ioContainer->statusReader = function () use ($statusFile) {
            return new FileReader($statusFile);
        };
        $this->ioContainer->statusWriter = function () use ($statusFile) {
            return new SimpleFileWriter($statusFile->getFilePath());
        };
        $this->ioContainer->logWriter = function () use ($logFile) {
            return new SimpleFileWriter($logFile->getFilePath());
        };

        $this->logFile = $logFile;
        $this->statusFile = $statusFile;
    }

    /**
     * デストラクタ
     */
    public function __destruct()
    {
        $this->directWrite();
    }

    /**
     * ログ設定を返却する
     * @return Container ログ設定
     */
    public function getConfig()
    {
        return $this->logConfig;
    }

    /**
     * 遅延書き出しを有効にする
     */
    public static function enableLazyWrite()
    {
        self::$logger->lazyWrite();
    }

    /**
     * 即時書き出しを有効にする
     */
    public static function enableDirectWrite()
    {
        self::$logger->directWrite();
    }

    /**
     * インスタンスを返却する
     * @return WebStream\Module\Logger ロガーインスタンス
     */
    public static function getInstance()
    {
        return self::$logger;
    }

    /**
     * ファイナライザ
     */
    public static function finalize()
    {
        self::$config = null;
        self::$logger = null;
        self::$formatter = null;
    }

    /**
     * Loggerを初期化する
     * @param Container ログ設定コンテナ
     */
    public static function init(Container $config)
    {
        self::$config = $config;
        self::$logger = new Logger($config);
        self::$formatter = new LoggerFormatter($config);
    }

    /**
     * Loggerが初期化済みかどうかチェックする
     * @param bool 初期化済みならtrue
     */
    public static function isInitialized()
    {
        return self::$logger !== null;
    }

    /**
     * Loggerメソッドの呼び出しを受ける
     * @param string メソッド名(ログレベル文字列)
     * @param array 引数
     */
    public static function __callStatic($level, $arguments)
    {
        if (self::$logger === null || self::$formatter === null) {
            if (self::$config !== null) {
                self::init(self::$config);
            } else {
                throw new LoggerException("Logger is not initialized.");
            }
        }

        call_user_func_array([self::$logger, "write"], array_merge([$level], $arguments));
    }

    /**
     * Outputterを設定する
     * @param array<IOutputter> $outputters Outputterリスト
     */
    public function setOutputter(array $outputters)
    {
        foreach ($outputters as $outputter) {
            if (!$outputter instanceof \WebStream\Log\Outputter\IOutputter) {
                throw new LoggerException("Log outputter must implement WebStream\Log\Outputter\IOutputter.");
            }
        }
        $this->outputters = $outputters;
    }

    /**
     * タイムスタンプを取得する
     * @return string タイムスタンプ
     */
    private function getTimeStamp()
    {
        date_default_timezone_set('Asia/Tokyo');
        $msec = sprintf("%2d", floatval(microtime()) * 100);

        return strftime("%Y-%m-%d %H:%M:%S") . "," . $msec;
    }

    /**
     * ログを書き出す
     * @param string ログレベル文字列
     * @param string 出力文字列
     * @param array<mixed> 埋め込み値リスト
     */
    public function write($level, $msg, $context = null)
    {
        if ($this->logConfig->logLevel > $this->toLogLevelValue($level)) {
            return;
        }

        if (is_array($context) && count($context) > 0) {
            // sprintfと同様の展開
            // [a-zA-Z0-9_-\.] 以外もキーには指定可能だが仕様としてこれ以外は不可とする
            preg_match_all('/\{\s*([a-zA-Z0-9._-]+)\s*?\}/', $msg, $matches);
            foreach ($matches[1] as $index => $value) {
                if (array_key_exists($value, $context)) {
                    $matches[1][$index] = $context[$value];
                } else {
                    unset($matches[0][$index]);
                }
            }
            $msg = str_replace($matches[0], $matches[1], $msg);
        }

        // ログローテート処理
        $this->rotate();

        try {
            if (count($this->outputters) > 0) {
                foreach ($this->outputters as $outputter) {
                    $outputter->write(self::$formatter->getFormattedMessage($msg, $level));
                }
            } else {
                $this->ioContainer->logWriter->write(self::$formatter->getFormattedMessage($msg, $level) . PHP_EOL);
            }
        } catch (IOException $e) {
            throw new LoggerException($e);
        }
    }

    /**
     * ログファイルをアーカイブする
     * stream.log -> stream.(作成日時)-(現在日時).log
     * @param string ログファイルパス
     */
    private function rotate()
    {
        // ログファイルがない場合はローテートしない
        if (!$this->logFile->exists()) {
            return;
        }

        // ログローテート実行
        if ($this->logConfig->rotateCycle !== null) {
            $this->rotateByCycle();
        } elseif ($this->logConfig->rotateSize !== null) {
            $this->rotateBySize();
        }
    }

    /**
     * ログステータスファイルに書きこむ
     * @throws IOException
     */
    private function writeStatus()
    {
        $this->ioContainer->statusWriter->write(intval(preg_replace('/^.*\s/', '', microtime())));
    }

    /**
     * ログステータスファイルを読み込む
     * @return int UnixTime
     * @throws LoggerException
     */
    private function readStatus()
    {
        $content = $this->ioContainer->statusReader->read();
        if (!preg_match('/^\d{10}$/', $content)) {
            throw new LoggerException("Invalid log state file contents: " . $content);
        }

        return intval($content);
    }

    /**
     * ステータスファイルを作成する
     */
    private function createStatusFile()
    {
        // ステータスファイルがない場合は書きだす
        if (!$this->statusFile->exists()) {
            $this->writeStatus();
        }
    }

    /**
     * ローテートを実行する
     * @param integer 作成日時のUnixTime
     * @param integer 現在日時のUnixTime
     */
    private function runRotate($from, $to)
    {
        $fromDate = date("YmdHis", $from);
        $toDate = date("YmdHis", $to);
        $archivePath = null;
        if (preg_match('/(.*)\.(.+)/', $this->logConfig->logPath, $matches)) {
            $archivePath = "$matches[1].${fromDate}-${toDate}.$matches[2]";
            // mvを実行
            $this->logFile->renameTo($archivePath);
            // ステータスファイルを削除
            $this->statusFile->delete();
        }
    }

    /**
     * 時間単位でローテートする
     * stream.log -> stream.(作成日時)-(現在日時).log
     */
    private function rotateByCycle()
    {
        $this->createStatusFile();
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $createdAt = $this->readStatus();

        $hour = intval(floor(($now - $createdAt) / 3600));
        if ($hour >= $this->logConfig->rotateCycle) {
            $this->runRotate($createdAt, $now);
        }
    }

    /**
     * サイズ単位でローテートする
     * stream.log -> stream.(作成日時)-(現在日時).log
     */
    private function rotateBySize()
    {
        $this->createStatusFile();
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $createdAt = $this->readStatus();

        $sizeKb = (int) floor($this->logFile->size() / 1024);
        // 指定したサイズより大きければローテート
        if ($sizeKb >= $this->logConfig->rotateSize) {
            $this->runRotate($createdAt, $now);
        }
    }

    /**
     * ログ出力パスを返却する
     * @return string ログ出力パス
     */
    public function getLogPath()
    {
        return $this->logConfig->logPath;
    }

    /**
     * ログローテートサイクルを返却する
     * @return string ログ出力パス
     */
    public function getLogRotateCycle()
    {
        return $this->logConfig->rotateCycle;
    }

    /**
     * ログローテートサイズを返却する
     * @return string ログ出力パス
     */
    public function getLogRotateSize()
    {
        return $this->logConfig->rotateSize;
    }

    /**
     * 遅延書き出しを有効にする
     */
    public function lazyWrite()
    {
        foreach ($this->outputters as $outputter) {
            if ($outputter instanceof \WebStream\Log\Outputter\ILazyWriter) {
                $outputter->enableLazyWrite();
            }
        }
    }

    /**
     * 即時書き出しを有効にする
     */
    public function directWrite()
    {
        foreach ($this->outputters as $outputter) {
            if ($outputter instanceof \WebStream\Log\Outputter\ILazyWriter) {
                $outputter->enableDirectWrite();
            }
        }
    }
}
