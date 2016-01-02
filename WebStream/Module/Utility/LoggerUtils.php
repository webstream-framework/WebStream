<?php
namespace WebStream\Module\Utility;

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
}
