<?php
namespace WebStream\Log;

use WebStream\IO\File;
use WebStream\IO\Writer\SimpleFileWriter;
use WebStream\Container\Container;
use WebStream\Exception\Extend\LoggerException;

/**
 * LoggerConfigurationManager
 * @author Ryuichi Tanaka
 * @since 2016/01/29
 * @version 0.7
 */
class LoggerConfigurationManager
{
    /**
     * @var Container ログ設定コンテナ
     */
    private $logContainer;

    /**
     * @var Container IOコンテナ
     */
    private $ioContainer;

    /**
     * @var array<string> ログ設定情報
     */
    private $configMap;

    /**
     * Constructor
     * @param mixed $config ログ設定
     * @throws LoggerException
     */
    public function __construct($config)
    {
        if (is_array($config)) {
            $configMap = $config;
        } else {
            $configMap = parse_ini_file($config);
            if ($configMap === null) {
                throw new LoggerException("Log config file does not exist: " . $config);
            }
        }

        $this->logContainer = new Container(false);
        $this->ioContainer = new Container();

        $this->ioContainer->file = function () use ($configMap) {
            if (!array_key_exists("path", $configMap)) {
                throw new LoggerException("Log path must be defined.");
            }
            return new File($configMap["path"]);
        };
        $this->ioContainer->fileWriter = function () use ($configMap) {
            return new SimpleFileWriter($configMap["path"]);
        };

        $this->configMap = $configMap;
    }

    /**
     * 設定を読み込む
     * @throws LoggerException
     */
    public function load()
    {
        $this->loadLogLevel()
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
     * ログレベルを読み込む
     * @throws LoggerException
     */
    private function loadLogLevel()
    {
        if (!array_key_exists("level", $this->configMap)) {
            throw new LoggerException("Log level must be defined.");
        }

        $logLevel = $this->toLogLevelValue($this->configMap["level"]);
        $this->logContainer->logLevel = $logLevel;

        return $this;
    }

    /**
     * ログ保存先パスを読み込む
     * @throws LoggerException
     */
    private function loadLogFilePath()
    {
        $file = $this->ioContainer->file;
        if (!($file->exists() && $file->isFile())) {
            $this->ioContainer->fileWriter->write("");
        }

        $this->logContainer->logPath = $file->getAbsoluteFilePath();
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
            $this->logContainer->rotateCycle = $this->cycle2value($this->configMap["rotate_cycle"]);
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
        if (array_key_exists("application_name", $this->configMap) && !empty($this->configMap["application_name"])) {
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
     * @throws LoggerException
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
                throw new LoggerException("Invalid log rotate cycle: " . $cycle);
        }
    }

    /**
     * ログレベルを数値に変換
     * ログレベルはWebStream独自、PSR-3両方対応
     * @param string ログレベル文字列
     * @throws LoggerException
     * @return int ログレベル数値
     */
    private function toLogLevelValue(string $level)
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
