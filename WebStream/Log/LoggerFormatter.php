<?php
namespace WebStream\Log;

use WebStream\Annotation\Base\IAnnotatable;

/**
 * LoggerFormatterクラス
 * ログフォーマッタ処理を行う
 * @author Ryuichi Tanaka
 * @since 2015/12/06
 * @version 0.7
 */
class LoggerFormatter implements IAnnotatable
{

    private $applicationName;

    public function __construct()
    {

    }

    /**
     * アプリケーション名を設定
     * %c
     * @Alias(name="c")
     */
    public function setApplicationName($applicationName)
    {
        $this->applicationName = $applicationName;
        var_dump($applicationName);
    }

    /**
     * %date_time%
     * @return string hoge
     */
    public function dateTime()
    {
        date_default_timezone_set('Asia/Tokyo');
        $msec = sprintf("%2d", floatval(microtime()) * 100);
        $dateTime = strftime("%Y-%m-%d %H:%M:%S") . "," . $msec;

        return $dateTime;
    }

    /**
     * %level%
     * @return [type] [description]
     */
    public function upperLevel()
    {
        echo "test1";
    }

    /**
     * %LEVEL%
     * @return [type] [description]
     */
    public function lowLevel()
    {
        echo "test2";

    }
}
