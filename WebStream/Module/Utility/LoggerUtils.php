<?php
namespace WebStream\Module\Utility;

use WebStream\Exception\Extend\LoggerException;

/**
 * LoggerUtils
 * ロガー依存のUtility
 * @author Ryuichi Tanaka
 * @since 2015/12/26
 * @version 0.7
 */
trait LoggerUtils
{
    /**
     * デフォルトロガーフォーマッタ
     * @return string デフォルトロガーフォーマッタ
     */
    public function defaultLoggerFormatter()
    {
        return '[%d{' . $this->defaultDateTimeFormatter() . '.%f}][%5L] %m';
    }

    /**
     * デフォルトDateTimeフォーマッタ
     * @return string デフォルトDateTimeフォーマッタ
     */
    public function defaultDateTimeFormatter()
    {
        return "%Y-%m-%d %H:%M:%S";
    }

    /**
     * ログメッセージにスタックトレースの内容を追加する
     * @param string ログメッセージ
     * @param string スタックトレース文字列
     * @return string 加工済みログメッセージ
     */
    public function addStackTrace($msg, $stacktrace)
    {
        // スタックトレースから原因となるエラー箇所のみ抽出
        $stacktraceList = explode("#", $stacktrace);
        foreach ($stacktraceList as $stacktraceLine) {
            if ($stacktraceLine === "") {
                continue;
            }
            $msg .= PHP_EOL;
            $msg .= "\t#" . trim($stacktraceLine);
        }

        return $msg;
    }

    /**
     * ログレベルを数値に変換
     * ログレベルはWebStream独自、PSR-3両方対応
     * @param string ログレベル文字列
     * @throws LoggerException
     * @return int ログレベル数値
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
}
