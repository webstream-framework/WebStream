<?php
namespace WebStream\Log;

use WebStream\Module\Utility\FileUtils;
use WebStream\Module\Utility\LoggerUtils;
use WebStream\Module\Container;
use WebStream\Exception\Extend\LoggerException;

/**
 * LoggerConfigurationManager
 * @author Ryuichi Tanaka
 * @since 2016/01/29
 * @version 0.7
 */
class LoggerConfigurationManager
{
    use FileUtils, LoggerUtils;

    /**
     * @var Container ログ設定コンテナ
     */
    private $logContainer;

    /**
     * @var array<string> ログ設定情報
     */
    private $configMap;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->logContainer = new Container(false);
        $this->configMap = [];
    }

    /**
     * 設定を読み込む
     * @param string $configPath 設定ファイル相対パス
     * @throws LoggerException
     */
    public function load($configPath)
    {
        $this->loadConfigFile($configPath)
             ->loadLogLevel()
             ->loadLogFilePath()
             ->loadRotateCycle()
             ->loadRotateSize()
             ->loadApplicationName()
             ->loadFormat();
    }

    /**
     * ログ設定を返却する
     * @return Container ログ設定
     */
    public function getConfig()
    {
        return $this->logContainer;
    }

    /**
     * 設定ファイルを読み込む
     * @param string $configPath 設定アイル相対パス
     * @throws LoggerException
     */
    private function loadConfigFile($configPath)
    {
        $configMap = $this->parseConfig($configPath);
        if ($configMap === null) {
            throw new LoggerException("Log config file does not exist: " . $configPath);
        }
        $this->configMap = $configMap;

        return $this;
    }

    /**
     * ログレベルを読み込む
     * @throws LoggerException
     */
    private function loadLogLevel()
    {
        if (!array_key_exists("level", $this->configMap)) {
            throw new LoggerException("Log level must be defined.");
        }

        $logLevel = $this->toLogLevelValue($this->configMap["level"]);
        if ($logLevel === 0) {
            throw new LoggerException("Invalid log level: " . $this->configMap["level"]);
        }
        $this->logContainer->logLevel = $logLevel;

        return $this;
    }

    /**
     * ログ保存先パスを読み込む
     * @throws LoggerException
     */
    private function loadLogFilePath()
    {
        if (!array_key_exists("path", $this->configMap)) {
            throw new LoggerException("Log path must be defined.");
        }

        $path = $this->getApplicationRoot() . "/" . $this->configMap["path"];
        if (!file_exists(dirname($path))) {
            throw new LoggerException("Log directory does not exist: " . dirname($path));
        }
        $this->logContainer->logPath = $path;

        $this->logContainer->statusPath = preg_replace_callback('/(.*)\..+/', function ($matches) {
            return "$matches[1].status";
        }, $this->logContainer->logPath);

        return $this;
    }

    /**
     * ログローテートサイクルを読み込む
     * @throws LoggerException
     */
    private function loadRotateCycle()
    {
        if (array_key_exists("rotate_cycle", $this->configMap)) {
            $rotateCycle = $this->cycle2value($this->configMap["rotate_cycle"]);
            // 妥当なローテートサイクルか
            if ($rotateCycle === 0) {
                throw new LoggerException("Invalid log rotate cycle: " . $this->configMap["rotate_cycle"]);
            }
            $this->logContainer->rotateCycle = $rotateCycle;
        }

        return $this;
    }

    /**
     * ログローテートサイズを読み込む
     * @throws LoggerException
     */
    private function loadRotateSize()
    {
        if (array_key_exists("rotate_size", $this->configMap)) {
            $rotateSize = intval($this->configMap["rotate_size"]);
            // ローテートサイズが不正の場合(正の整数以外の値が設定された場合)
            if ($rotateSize <= 0) {
                throw new LoggerException("Invalid log rotate size: " . $this->configMap["rotate_size"]);
            }
            $this->logContainer->rotateSize = $rotateSize;
        }

        return $this;
    }

    /**
     * アプリケーション名を読み込む
     */
    private function loadApplicationName()
    {
        if (array_key_exists("application_name", $this->configMap)) {
            $this->logContainer->applicationName = $this->configMap["application_name"];
        }

        return $this;
    }

    /**
     * ロガーフォーマットを読み込む
     */
    private function loadFormat()
    {
        if (array_key_exists("format", $this->configMap)) {
            $this->logContainer->format = $this->configMap["format"];
        } else {
            $this->logContainer->format = $this->defaultLoggerFormatter();
        }

        return $this;
    }

    /**
     * ログローテートサイクルを時間に変換
     * @param string ローテートサイクル
     * @return int ローテート時間
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
}
