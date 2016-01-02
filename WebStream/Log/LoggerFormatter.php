<?php
namespace WebStream\Log;

use WebStream\Module\Utility\FileUtils;
use WebStream\Module\Utility\LoggerUtils;
use WebStream\Exception\Extend\LoggerException;

/**
 * LoggerFormatterクラス
 * ログフォーマッタ処理を行う
 * @author Ryuichi Tanaka
 * @since 2015/12/06
 * @version 0.7
 */
class LoggerFormatter
{
    use FileUtils;
    use LoggerUtils;

    /**
     * @var string ログフォーマット
     */
    private $format;

    /**
     * @var アプリケーション名
     */
    private $applicationName;

    /**
     * コンストラクタ
     * @param string 設定ファイルパス
     */
    public function __construct($configPath)
    {
        $this->loadConfig($configPath);
        $this->compile();
    }

    /**
     * フォーマット済みメッセージを返却する
     * @param  string メッセージ
     * @param  string ログレベル
     * @return フォーマット済みメッセージ
     */
    public function getFormattedMessage($message, $logLevel)
    {
        $formattedMessage = $this->format;

        // 日付
        $formattedMessage = $this->compileDateTime($formattedMessage);

        // ログレベル
        $formattedMessage = $this->compileLogLevel($formattedMessage, $logLevel);

        // メッセージ
        $formattedMessage = preg_replace('/%m/', $message, $formattedMessage);

        return $formattedMessage . "\n";
    }

    /**
     * 設定ファイルを読み込む
     * @param string 設定ファイルパス
     */
    private function loadConfig($configPath)
    {
        $log = $this->parseConfig($configPath);

        // 設定ファイルが存在するかどうか
        if ($log === null) {
            throw new LoggerException("Log config file does not exist: " . $configPath);
        }

        // ログアプリケーション名
        if (isset($log["applicationName"])) {
            $this->applicationName = $log["applicationName"];
        }

        // ログフォーマット
        if (isset($log["format"])) {
            $this->format = $log["format"];
        } else {
            $this->format = $this->defaultLoggerFormatter();
        }
    }

    /**
     * 固定の項目を埋め込む
     */
    private function compile()
    {
        $this->format = $this->compileApplicationName($this->format, $this->applicationName);
    }

    /**
     * アプリケーション名項目を埋め込む
     * @param  string メッセージ
     * @param  string アプリケーション名
     * @return 埋め込み済みメッセージ
     */
    private function compileApplicationName($message, $applicationName)
    {
        // アプリケーション名
        if ($applicationName !== null && preg_match('/%([0-9]{0,})c/', $this->format, $matches)) {
            $applicationName = $matches[1] !== null ? str_pad($applicationName, intval($matches[1]), ' ') : $applicationName;
            $message = preg_replace('/%(?:[0-9]{0,})c/', $applicationName, $message);
        }

        return $message;
    }

    /**
     * 日付項目を埋め込む
     * @param  string メッセージ
     * @return 埋め込み済みメッセージ
     */
    private function compileDateTime($message)
    {
        if (preg_match('/%([0-9]{0,})d(?:\{(.+?)\}){1}/', $message, $formatMatches)) {
            $message = preg_replace('/%[0-9]{0,}d/', '%d', $message);
            $now = microtime(true);
            if (preg_match('/^[0-9]*\\.([0-9]+)$/', $now, $matches)) {
                $decimal = str_pad(substr($matches[1], 0, 3), 3, "0");
            } else {
                $decimal = "000";
            }
            $dateTimeFormat = preg_replace('/(%f)/', $decimal, $formatMatches[2]);
            $dateTime = strftime($dateTimeFormat, $now);
            $dateTime = empty($formatMatches[1]) ? $dateTime : str_pad($dateTime, $formatMatches[1], ' ');
            $message = preg_replace('/%d\{.+?\}/', $dateTime, $message);
        } elseif (preg_match('/%([0-9]{0,})d/', $message, $formatMatches)) {
            $message = preg_replace('/%[0-9]{0,}d/', '%d', $message);
            $dateTime = strftime($this->defaultDateTimeFormatter());
            $dateTime = empty($formatMatches[1]) ? $dateTime : str_pad($dateTime, $formatMatches[1], ' ');
            $message = preg_replace('/%d/', $dateTime, $message);
        }

        return $message;
    }

    /**
     * ログレベル項目を埋め込む
     * @param  string メッセージ
     * @param  string ログレベル
     * @return 埋め込み済みメッセージ
     */
    private function compileLogLevel($message, $logLevel)
    {
        // ログレベル
        if (preg_match('/%([0-9]{0,})L/', $message, $matches)) {
            $upperLevel = strtoupper(empty($matches[1]) ? $logLevel : str_pad($logLevel, $matches[1], ' '));
            $message = preg_replace('/%([0-9]{0,})L/', $upperLevel, $message);
        }
        if (preg_match('/%([0-9]{0,})l/', $message, $matches)) {
            $lowerLevel = empty($matches[1]) ? $logLevel : str_pad($logLevel, $matches[1], ' ');
            $message = preg_replace('/%([0-9]{0,})l/', $lowerLevel, $message);
        }

        return $message;
    }
}
