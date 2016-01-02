<?php
namespace WebStream\Log;

use WebStream\Module\Utility\FileUtils;
use WebStream\Module\Container;
use WebStream\DI\ServiceLocator;
use WebStream\Exception\Extend\LoggerException;

/**
 * Loggerクラス
 * @author Ryuichi Tanaka
 * @since 2012/01/16
 * @version 0.7
 */
class Logger
{
    use FileUtils;

    /**
     * @var Logger ロガー
     */
    private static $logger;

    /**
     * @var LoggerFormatter ロガーフォーマッタ
     */
    private static $formatter;

    /**
     * @var string 設定ファイルパス
     */
    private static $configPath;

    /**
     * @var string ログファイルパス
     */
    private $logPath;

    /**
     * @var string ログレベル
     */
    private $logLevel;

    /**
     * @var string ローテートサイクル
     */
    private $rotateCycle;

    /**
     * @var int ログローテートサイズ
     */
    private $rotateSize;

    /**
     * @var string ログステータスファイルパス
     */
    private $statusPath;

    /**
     * コンストラクタ
     * @param string ログファイルパス
     */
    private function __construct($configPath)
    {
        $this->loadCofig($configPath);
    }

    // /**
    //  * デストラクタ
    //  */
    // public function __destruct()
    // {
    //     Logger::finalize();
    // }

    /**
     * インスタンスを返却する
     * @return WebStream\Module\Logger ロガーインスタンス
     */
    public static function getInstance()
    {
        return self::$logger;
    }

    /**
     * Loggerを初期化する
     * @param string 設定ファイルパス
     */
    public static function init($configPath = "config/log.ini")
    {
        self::$configPath = $configPath;
        self::$logger = new Logger($configPath);
        self::$formatter = new LoggerFormatter($configPath);
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
     * Loggerを終了する
     */
    public static function finalize()
    {
        self::$logger = null;
        self::$formatter = null;
    }

    /**
     * Loggerメソッドの呼び出しを受ける
     * @param string メソッド名(ログレベル文字列)
     * @param array 引数
     */
    public static function __callStatic($level, $arguments)
    {
        if (self::$logger === null || self::$formatter === null) {
            if (self::$configPath !== null) {
                self::init(self::$configPath);
            } else {
                throw new LoggerException("Logger is not initialized.");
            }
        }

        call_user_func_array([self::$logger, "write"], array_merge([$level], $arguments));
    }

    /**
     * 設定ファイルを読み込む
     * @param string 設定ファイルパス
     */
    private function loadCofig($configPath)
    {
        $log = $this->parseConfig($configPath);

        // 設定ファイルが存在するかどうか
        if ($log === null) {
            throw new LoggerException("Log config file does not exist: " . $configPath);
        }

        // ログレベル取得
        $logLevel = $this->toLogLevelValue($log["level"]);
        // 妥当なログレベルかどうか
        if ($logLevel === 0) {
            throw new LoggerException("Invalid log level: " . $log["level"]);
        }
        $this->logLevel = $logLevel;

        // パスを取得
        $path = $log["path"];
        // 絶対パスでのチェック
        if (!realpath(dirname($path))) {
            $container = ServiceLocator::getInstance()->getContainer();
            $path = $container->applicationInfo->applicationRoot . "/" . $log["path"];
            // プロジェクトルートからの相対パスでのチェック
            if (!file_exists(dirname($path))) {
                throw new LoggerException("Log directory does not exist: " . dirname($path));
            }
        }
        $this->logPath = $path;

        // ステータスファイルパスを設定
        $this->statusPath = preg_replace_callback('/(.*)\..+/', function ($matches) {
            return "$matches[1].status";
        }, $this->logPath);

        // ログローテート設定(時間)
        if (isset($log["rotate_cycle"])) {
            $rotateCycle = $this->cycle2value($log["rotate_cycle"]);
            // 妥当なローテートサイクルか
            if ($rotateCycle === 0) {
                throw new LoggerException("Invalid log rotate cycle: " . $log["rotate_cycle"]);
            }
            $this->rotateCycle = $rotateCycle;
        }

        // ログローテート
        if (isset($log["rotate_size"])) {
            $rotateSize = intval($log["rotate_size"]);
            // ローテートサイズが不正の場合(正の整数以外の値が設定された場合)
            if ($rotateSize <= 0) {
                throw new LoggerException("Invalid log rotate size: " . $log["rotate_size"]);
            }
            $this->rotateSize = $rotateSize;
        }
    }

    /**
     * ログレベルを数値に変換
     * ログレベルはWebStream独自、PSR-3両方対応
     * @param string ログレベル文字列
     * @return integer ログレベル数値
     */
    public function toLogLevelValue(string $level)
    {
        switch (strtolower($level)) {
            case 'debug':
                return 1;
            case 'info':
                return 2;
            case 'notice':    // PSR-3
                return 3;
            case 'warn':
            case 'warning':   // PSR-3
                return 4;
            case 'error':
                return 5;
            case 'critical':  // PSR-3
                return 6;
            case 'alert':     // PSR-3
                return 7;
            case 'emergency': // PSR-3
                return 8;
            case 'fatal':
                return 9;
            default:
                throw new LoggerException("Undefined log level: $level");
        }
    }

    /**
     * ログローテートサイクルを時間に変換
     * @param string ローテートサイクル
     * @return integer ローテート時間
     */
    private function cycle2value($cycle)
    {
        $day_to_h = 24;
        $week_to_h = $day_to_h * 7;
        $month_to_h = $day_to_h * intval(date("t", time()));
        $year_to_h = $day_to_h * 365;

        $year = date("Y");
        if (($year % 4 === 0 && $year % 100 !== 0) || $year % 400 === 0) {
            $year_to_h = $day_to_h * 366;
        }

        switch (strtolower($cycle)) {
            case 'day':
                return $day_to_h;
            case 'week':
                return $week_to_h;
            case 'month':
                return $month_to_h;
            case 'year':
                return $year_to_h;
            default:
                return 0;
        }
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
     * ログメッセージにスタックトレースの内容を追加する
     * @param string ログメッセージ
     * @param string スタックトレース文字列
     * @return string 加工済みログメッセージ
     */
    private function message($msg, $stacktrace = null)
    {
        // スタックトレースから原因となるエラー箇所のみ抽出
        $stacktraceList = explode("#", $stacktrace);
        foreach ($stacktraceList as $stacktraceLine) {
            if ($stacktraceLine === "") {
                continue;
            }
            $msg .= "\n";
            $msg .= "\t#" . trim($stacktraceLine);
        }

        return $msg;
    }

    /**
     * ログを書き出す
     * @param string ログパス
     * @param string ログレベル文字列
     * @param string 書きだす文字列
     * @param string スタックトレース文字列
     */
    public function write($level, $msg, $context = null)
    {
        if ($this->logLevel > $this->toLogLevelValue($level)) {
            return;
        }

        if (is_array($context)) {
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
        } else {
            $msg = $this->message($msg, $context);
        }

        $this->rotate();
        try {
            @error_log(self::$formatter->getFormattedMessage($msg, $level), 3, $this->logPath);
        } catch (\Exception $e) {
            throw new LoggerException($e);
        }
    }

    /**
     * ログステータスファイルに書きこむ
     */
    private function writeStatus()
    {
        file_put_contents($this->statusPath, intval(preg_replace('/^.*\s/', '', microtime())));
    }

    /**
     * ログステータスファイルを読み込む
     */
    private function readStatus()
    {
        $handle = fopen($this->statusPath, "r");
        $size = filesize($this->statusPath);
        $content = fread($handle, $size);
        fclose($handle);
        if (!preg_match('/^\d{10}$/', $content)) {
            throw new LoggerException("Invalid log state file contents: " . $content);
        }

        return intval($content);
    }

    /**
     * ステータスファイルを作成する
     */
    private function createstatusPath()
    {
        // ステータスファイルがない場合は書きだす
        if (!is_file($this->statusPath)) {
            $this->writeStatus();
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
        if (!realpath($this->logPath)) {
            return;
        }
        // ログローテート実行
        if ($this->rotateCycle !== null) {
            $this->rotateByCycle();
        } elseif ($this->rotateSize !== null) {
            $this->rotateBySize();
        }
    }

    /**
     * ローテートを実行する
     * @param integer 作成日時のUnixTime
     * @param integer 現在日時のUnixTime
     */
    private function runRotate($from, $to)
    {
        $from_date = date("YmdHis", $from);
        $to_date = date("YmdHis", $to);
        $archive_path = null;
        if (preg_match('/(.*)\.(.+)/', $this->logPath, $matches)) {
            $archive_path = "$matches[1].${from_date}-${to_date}.$matches[2]";
            // mvを実行
            rename($this->logPath, $archive_path);
            // ステータスファイルを削除
            unlink($this->statusPath);
        }
    }

    /**
     * 時間単位でローテートする
     * stream.log -> stream.(作成日時)-(現在日時).log
     */
    private function rotateByCycle()
    {
        $this->createstatusPath();
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $createdAt = $this->readStatus($this->statusPath);

        // ローテート周期を過ぎている場合はログファイルをアーカイブする
        $hour = intval(floor(($now - $createdAt) / 3600));
        if ($hour >= $this->rotateCycle) {
            $this->runRotate($createdAt, $now);
        }
    }

    /**
     * サイズ単位でローテートする
     * stream.log -> stream.(作成日時)-(現在日時).log
     */
    private function rotateBySize()
    {
        $this->createstatusPath();
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $createdAt = $this->readStatus();

        $size_kb = (int) floor(filesize($this->logPath) / 1024);
        // 指定したサイズより大きければローテート
        if ($size_kb >= $this->rotateSize) {
            $this->runRotate($createdAt, $now);
        }
    }

    /**
     * ログ出力パスを返却する
     * @return string ログ出力パス
     */
    public function getLogPath()
    {
        return $this->logPath;
    }

    /**
     * ログローテートサイクルを返却する
     * @return string ログ出力パス
     */
    public function getLogRotateCycle()
    {
        return $this->rotateCycle;
    }

    /**
     * ログローテートサイズを返却する
     * @return string ログ出力パス
     */
    public function getLogRotateSize()
    {
        return $this->rotateSize;
    }
}
