<?php
namespace WebStream\Log;

use WebStream\Exception\Extend\LoggerException;

/**
 * LoggerUtils
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
