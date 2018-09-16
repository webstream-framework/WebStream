<?php
namespace WebStream\Log;

use WebStream\Container\Container;

/**
 * LoggerFormatterクラス
 * ログフォーマッタ処理を行う
 * @author Ryuichi Tanaka
 * @since 2015/12/06
 * @version 0.7
 */
class LoggerFormatter
{
    use LoggerUtils;

    /**
     * @var Container ログ設定コンテナ
     */
    private $logConfig;

    /**
     * コンストラクタ
     * @param string 設定ファイルパス
     */
    public function __construct(Container $logConfig)
    {
        $this->logConfig = $logConfig;
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
        $formattedMessage = $this->logConfig->format;

        // 日付
        $formattedMessage = $this->compileDateTime($formattedMessage);

        // ログレベル
        $formattedMessage = $this->compileLogLevel($formattedMessage, $logLevel);

        // メッセージ
        $formattedMessage = preg_replace('/%m/', $message, $formattedMessage);

        return $formattedMessage . PHP_EOL;
    }

    /**
     * 固定の項目を埋め込む
     */
    private function compile()
    {
        $this->logConfig->format = $this->compileApplicationName($this->logConfig->format, $this->logConfig->applicationName);
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
        if ($applicationName !== null && preg_match('/%([0-9]{0,})c/', $this->logConfig->format, $matches)) {
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
            $decimal = "000";
            if (preg_match('/^[0-9]*\\.([0-9]+)$/', $now, $matches) === 1) {
                $decimal = str_pad(substr($matches[1], 0, 3), 3, "0");
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
