<?php
namespace WebStream\Test;

use WebStream\Module\Logger;
use WebStream\Module\Utility;
use WebStream\Test\DataProvider\LoggerProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/LoggerProvider.php';

/**
 * LoggerTest
 * @author Ryuichi TANAKA.
 * @since 2011/08/25
 * @version 0.4
 */
class LoggerTest extends TestBase
{
    use Utility, LoggerProvider, TestConstant;

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        $logPath = $this->getRoot() . "/" . $this->getLogFilePath();
        $handle = fopen($logPath, "w+");
        fclose($handle);
        chmod($logPath, 0777);
    }

    private function logTail($configPath)
    {
        $log = $this->parseConfig($configPath);
        $logPath = $this->getRoot() . "/" . $log["path"];
        $file = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return array_pop($file);
    }

    private function write($level, $config_path, $msg, $stacktrace = null)
    {
        Logger::init($config_path);
        if ($level === "DEBUG") {
            Logger::debug($msg, $stacktrace);
        } elseif ($level === "INFO") {
            Logger::info($msg, $stacktrace);
        } elseif ($level === "WARN") {
            Logger::warn($msg, $stacktrace);
        } elseif ($level === "ERROR") {
            Logger::error($msg, $stacktrace);
        } elseif ($level === "FATAL") {
            Logger::fatal($msg, $stacktrace);
        }
    }

    /**
     * 正常系
     * ログレベルが「debug」のとき、
     * 「debug」「info」「warn」「error」「fatal」レベルのログが書き出せること
     * @test
     * @dataProvider logLevelDebugProvider
     */
    public function okWriteDebug($level, $configPath, $msg, $stacktrace = null)
    {
        $configPath = $this->getLogConfigPath() . "/" . $configPath;
        $this->write($level, $configPath, $msg, $stacktrace);
        $lineTail = $this->logTail($configPath);
        if ($stacktrace === null) {
            if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},.{1,2}\]\s\[(.+?)\]\s(.*)$/', $lineTail, $matches)) {
                $target = [$level, $msg];
                $result = [$matches[1], $matches[2]];
                $this->assertEquals($target, $result);
            } else {
                $this->assertTrue(false);
            }
        } else {
            if (preg_match('/^(\t#\d.*)/', $lineTail, $matches)) {
                $this->assertEquals($lineTail, $matches[1]);
            } else {
                $this->assertTrue(false);
            }
        }
    }

    /**
     * 正常系
     * ログレベルが「info」のとき、
     * 「info」「warn」「error」「fatal」レベルのログが書き出せること
     * @test
     * @dataProvider logLevelInfoProvider
     */
    public function okWriteInfo($level, $configPath, $msg, $stacktrace = null)
    {
        $configPath = $this->getLogConfigPath() . "/" . $configPath;
        $this->write($level, $configPath, $msg, $stacktrace);
        $lineTail = $this->logTail($configPath);

        if ($level === "DEBUG") {
            $this->assertNull($lineTail);
        } else {
            if ($stacktrace === null) {
                if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},.{1,2}\]\s\[(.+?)\]\s(.*)$/', $lineTail, $matches)) {
                    $target = [$level, $msg];
                    $result = [$matches[1], $matches[2]];
                    $this->assertEquals($target, $result);
                } else {
                    $this->assertTrue(false);
                }
            } else {
                if (preg_match('/^(\t#\d.*)/', $lineTail, $matches)) {
                    $this->assertEquals($lineTail, $matches[1]);
                } else {
                    $this->assertTrue(false);
                }
            }
        }
    }

    /**
     * 正常系
     * ログレベルが「warn」のとき、
     * 「warn」「error」「fatal」レベルのログが書き出せること
     * @test
     * @dataProvider logLevelWarnProvider
     */
    public function okWriteWarn($level, $configPath, $msg, $stacktrace = null)
    {
        $configPath = $this->getLogConfigPath() . "/" . $configPath;
        $this->write($level, $configPath, $msg, $stacktrace);
        $lineTail = $this->logTail($configPath);

        if ($level === "DEBUG" || $level === "INFO") {
            $this->assertNull($lineTail);
        } else {
            if ($stacktrace === null) {
                if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},.{1,2}\]\s\[(.+?)\]\s(.*)$/', $lineTail, $matches)) {
                    $target = [$level, $msg];
                    $result = [$matches[1], $matches[2]];
                    $this->assertEquals($target, $result);
                } else {
                    $this->assertTrue(false);
                }
            } else {
                if (preg_match('/^(\t#\d.*)/', $lineTail, $matches)) {
                    $this->assertEquals($lineTail, $matches[1]);
                } else {
                    $this->assertTrue(false);
                }
            }
        }
    }

    /**
     * 正常系
     * ログレベルが「error」のとき、
     * 「error」「fatal」レベルのログが書き出せること
     * @test
     * @dataProvider logLevelErrorProvider
     */
    public function okWriteError($level, $configPath, $msg, $stacktrace = null)
    {
        $configPath = $this->getLogConfigPath() . "/" . $configPath;
        $this->write($level, $configPath, $msg, $stacktrace);
        $lineTail = $this->logTail($configPath);

        if ($level === "DEBUG" || $level === "INFO" || $level === "WARN") {
            $this->assertNull($lineTail);
        } else {
            if ($stacktrace === null) {
                if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},.{1,2}\]\s\[(.+?)\]\s(.*)$/', $lineTail, $matches)) {
                    $target = [$level, $msg];
                    $result = [$matches[1], $matches[2]];
                    $this->assertEquals($target, $result);
                } else {
                    $this->assertTrue(false);
                }
            } else {
                if (preg_match('/^(\t#\d.*)/', $lineTail, $matches)) {
                    $this->assertEquals($lineTail, $matches[1]);
                } else {
                    $this->assertTrue(false);
                }
            }
        }
    }

    /**
     * 正常系
     * ログレベルが「fatal」のとき、
     * 「fatal」レベルのログが書き出せること
     * @test
     * @dataProvider logLevelFatalProvider
     */
    public function testOkWriteFatal($level, $configPath, $msg, $stacktrace = null)
    {
        $configPath = $this->getLogConfigPath() . "/" . $configPath;
        $this->write($level, $configPath, $msg, $stacktrace);
        $lineTail = $this->logTail($configPath);

        if ($level === "DEBUG" || $level === "INFO" || $level === "WARN" || $level === "ERROR") {
            $this->assertNull($lineTail);
        } else {
            if ($stacktrace === null) {
                if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},.{1,2}\]\s\[(.+?)\]\s(.*)$/', $lineTail, $matches)) {
                    $target = [$level, $msg];
                    $result = [$matches[1], $matches[2]];
                    $this->assertEquals($target, $result);
                } else {
                    $this->assertTrue(false);
                }
            } else {
                if (preg_match('/^(\t#\d.*)/', $lineTail, $matches)) {
                    $this->assertEquals($lineTail, $matches[1]);
                } else {
                    $this->assertTrue(false);
                }
            }
        }
    }

    /**
     * 正常系
     * ログ設定ファイルにローテート設定が日単位かつ
     * ログファイル作成日が24時間以内の場合、
     * ログローテートは実行されないこと
     * @test
     * @dataProvider rotateCycleDayWithinProvider
     */
    public function okRotateCycleWithinDay($configPath, $hour)
    {
        // 既存のステータスファイルは削除
        $statusPath = $this->getRoot() . $this->getSampleAppPath() . "/log/webstream.test.status";
        if (file_exists($statusPath)) {
            unlink($statusPath);
        }
        // 現在時刻より$hour時間前のUnixTimeを取得
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $created_at = $now - 3600 * $hour;
        $created_at_date = date("YmdHis", $created_at);
        $now_date = date("YmdHis", $now);
        // ローテートファイル名(作成されないが)
        $rotatedLogPath = $this->getRoot() . $this->getSampleAppPath() . "/log/webstream.test.${created_at_date}-${now_date}.log";
        // テスト用のステータスファイルを作成
        file_put_contents($statusPath, $created_at);
        // ログ書き出し
        $configPath = $this->getLogConfigPath() . "/" . $configPath;
        $this->write("INFO", $configPath, "test");
        // ローテートされたかチェック
        $this->assertFalse(file_exists($rotatedLogPath));
    }

    /**
     * 正常系
     * ログ設定ファイルにローテート設定が日単位かつ
     * ログファイル作成日が24時間以上の場合、
     * ログローテートが実行されること
     * @test
     * @dataProvider rotateCycleDayProvider
     */
    public function okRotateCycleDay($configPath, $hour)
    {
        // 既存のステータスファイルは削除
        $statusPath = $this->getRoot() . $this->getSampleAppPath() . "/log/webstream.test.status";
        if (file_exists($statusPath)) {
            unlink($statusPath);
        }
        // 現在時刻より$hour時間前のUnixTimeを取得
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $created_at = $now - 3600 * $hour;
        $created_at_date = date("YmdHis", $created_at);
        $now_date = date("YmdHis", $now);
        // ローテートファイル名(作成されないが)
        $rotatedLogPath = $this->getRoot() . $this->getSampleAppPath() . "/log/webstream.test.${created_at_date}-${now_date}.log";
        // テスト用のステータスファイルを作成
        file_put_contents($statusPath, $created_at);
        // ログ書き出し
        $configPath = $this->getLogConfigPath() . "/" . $configPath;
        $this->write("INFO", $configPath, "test");
        // ローテートされたかチェック
        $this->assertFileExists($rotatedLogPath);
        // ローテートしたログファイルを削除
        if (file_exists($rotatedLogPath)) {
            unlink($rotatedLogPath);
        }
    }

    /**
     * 正常系
     * ログ設定ファイルにローテート設定が
     * 週単位かつログファイル作成日が1週間以内の場合、
     * ログローテートは実行されないこと
     * @test
     * @dataProvider rotateCycleWeekWithinProvider
     */
    public function okRotateCycleWithinWeek($configPath, $hour)
    {
        // 既存のステータスファイルは削除
        $statusPath = $this->getRoot() . $this->getSampleAppPath() . "/log/webstream.test.status";
        if (file_exists($statusPath)) {
            unlink($statusPath);
        }
        // 現在時刻より$hour時間前のUnixTimeを取得
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $created_at = $now - 3600 * $hour;
        $created_at_date = date("YmdHis", $created_at);
        $now_date = date("YmdHis", $now);
        // ローテートファイル名(作成されないが)
        $rotatedLogPath = $this->getRoot() . $this->getSampleAppPath() . "/log/webstream.test.${created_at_date}-${now_date}.log";
        // テスト用のステータスファイルを作成
        file_put_contents($statusPath, $created_at);
        // ログ書き出し
        $configPath = $this->getLogConfigPath() . "/" . $configPath;
        $this->write("INFO", $configPath, "test");
        // ローテートされたかチェック
        $this->assertFalse(file_exists($rotatedLogPath));
    }

    /**
     * 正常系
     * ログ設定ファイルにローテート設定が週単位かつ
     * ログファイル作成日が1週間以上の場合、
     * ログローテートが実行されること
     * @test
     * @dataProvider rotateCycleWeekProvider
     */
    public function okRotateCycleWeek($configPath, $hour)
    {
        // 既存のステータスファイルは削除
        $statusPath = $this->getRoot() . $this->getSampleAppPath() . "/log/webstream.test.status";
        if (file_exists($statusPath)) {
            unlink($statusPath);
        }
        // 現在時刻より$hour時間前のUnixTimeを取得
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $created_at = $now - 3600 * $hour;
        $created_at_date = date("YmdHis", $created_at);
        $now_date = date("YmdHis", $now);
        // ローテートファイル名(作成されないが)
        $rotatedLogPath = $this->getRoot() . $this->getSampleAppPath() . "/log/webstream.test.${created_at_date}-${now_date}.log";
        // テスト用のステータスファイルを作成
        file_put_contents($statusPath, $created_at);
        // ログ書き出し
        $configPath = $this->getLogConfigPath() . "/" . $configPath;
        $this->write("INFO", $configPath, "test");
        // ローテートされたかチェック
        $this->assertFileExists($rotatedLogPath);
        // ローテートしたログファイルを削除
        if (file_exists($rotatedLogPath)) {
            unlink($rotatedLogPath);
        }
    }

    /**
     * 正常系
     * ログ設定ファイルにローテート設定が月単位かつ
     * ログファイル作成日が1ヶ月以内の場合、
     * ログローテートは実行されないこと
     * @test
     * @dataProvider rotateCycleMonthWithinProvider
     */
    public function okRotateCycleWithinMonth($configPath, $hour)
    {
        // 既存のステータスファイルは削除
        $statusPath = $this->getRoot() . $this->getSampleAppPath() . "/log/webstream.test.status";
        if (file_exists($statusPath)) {
            unlink($statusPath);
        }
        // 現在時刻より$hour時間前のUnixTimeを取得
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $created_at = $now - 3600 * $hour;
        $created_at_date = date("YmdHis", $created_at);
        $now_date = date("YmdHis", $now);
        // ローテートファイル名(作成されないが)
        $rotatedLogPath = $this->getRoot() . $this->getSampleAppPath() . "/log/webstream.test.${created_at_date}-${now_date}.log";
        // テスト用のステータスファイルを作成
        file_put_contents($statusPath, $created_at);
        // ログ書き出し
        $configPath = $this->getLogConfigPath() . "/" . $configPath;
        $this->write("INFO", $configPath, "test");
        // ローテートされたかチェック
        $this->assertFalse(file_exists($rotatedLogPath));
    }

    /**
     * 正常系
     * ログ設定ファイルにローテート設定が月単位かつ
     * ログファイル作成日が1ヶ月以上の場合、
     * ログローテートが実行されること
     * @test
     * @dataProvider rotateCycleMonthProvider
     */
    public function okRotateCycleMonth($configPath, $hour)
    {
        // 既存のステータスファイルは削除
        $statusPath = $this->getRoot() . $this->getSampleAppPath() . "/log/webstream.test.status";
        if (file_exists($statusPath)) {
            unlink($statusPath);
        }
        // 現在時刻より$hour時間前のUnixTimeを取得
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $created_at = $now - 3600 * $hour;
        $created_at_date = date("YmdHis", $created_at);
        $now_date = date("YmdHis", $now);
        // ローテートファイル名(作成されないが)
        $rotatedLogPath = $this->getRoot() . $this->getSampleAppPath() . "/log/webstream.test.${created_at_date}-${now_date}.log";
        // テスト用のステータスファイルを作成
        file_put_contents($statusPath, $created_at);
        // ログ書き出し
        $configPath = $this->getLogConfigPath() . "/" . $configPath;
        $this->write("INFO", $configPath, "test");
        // ローテートされたかチェック
        $this->assertFileExists($rotatedLogPath);
        // ローテートしたログファイルを削除
        if (file_exists($rotatedLogPath)) {
            unlink($rotatedLogPath);
        }
    }

    /**
     * 正常系
     * ログ設定ファイルにローテート設定が年単位かつ
     * ログファイル作成日が1年以内の場合、
     * ログローテートは実行されないこと
     * @test
     * @dataProvider rotateCycleYearWithinProvider
     */
    public function okRotateCycleWithinYear($configPath, $hour)
    {
        // 既存のステータスファイルは削除
        $statusPath = $this->getRoot() . $this->getSampleAppPath() . "/log/webstream.test.status";
        if (file_exists($statusPath)) {
            unlink($statusPath);
        }
        // 現在時刻より$hour時間前のUnixTimeを取得
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $created_at = $now - 3600 * $hour;
        $created_at_date = date("YmdHis", $created_at);
        $now_date = date("YmdHis", $now);
        // ローテートファイル名(作成されないが)
        $rotatedLogPath = $this->getRoot() . $this->getSampleAppPath() . "/log/webstream.test.${created_at_date}-${now_date}.log";
        // テスト用のステータスファイルを作成
        file_put_contents($statusPath, $created_at);
        // ログ書き出し
        $configPath = $this->getLogConfigPath() . "/" . $configPath;
        $this->write("INFO", $configPath, "test");
        // ローテートされたかチェック
        $this->assertFalse(file_exists($rotatedLogPath));
    }

    /**
     * 正常系
     * ログ設定ファイルにローテート設定が年単位かつ
     * ログファイル作成日が1年以上の場合、
     * ログローテートが実行されること
     * @test
     * @dataProvider rotateCycleYearProvider
     */
    public function testOkRotateCycleYear($configPath, $hour)
    {
        // 既存のステータスファイルは削除
        $statusPath = $this->getRoot() . $this->getSampleAppPath() . "/log/webstream.test.status";
        if (file_exists($statusPath)) {
            unlink($statusPath);
        }
        // 現在時刻より$hour時間前のUnixTimeを取得
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $created_at = $now - 3600 * $hour;
        $created_at_date = date("YmdHis", $created_at);
        $now_date = date("YmdHis", $now);
        // ローテートファイル名(作成されないが)
        $rotatedLogPath = $this->getRoot() . $this->getSampleAppPath() . "/log/webstream.test.${created_at_date}-${now_date}.log";
        // テスト用のステータスファイルを作成
        file_put_contents($statusPath, $created_at);
        // ログ書き出し
        $configPath = $this->getLogConfigPath() . "/" . $configPath;
        $this->write("INFO", $configPath, "test");
        // ローテートされたかチェック
        $this->assertFileExists($rotatedLogPath);
        // ローテートしたログファイルを削除
        if (file_exists($rotatedLogPath)) {
            unlink($rotatedLogPath);
        }
    }

    /**
     * 正常系
     * ログ設定ファイルにローテート設定(サイズ単位)されていて、現在のログサイズが
     * 指定値以上の場合、ログローテートが実行されること
     * @test
     * @dataProvider rotateSizeProvider
     */
    public function okRotateSize($configPath, $byte)
    {
        // ログファイルに1024バイトのデータを書き込む
        $logPath = $this->getRoot() . "/" . $this->getLogFilePath();
        $handle = fopen($logPath, "w");
        for ($i = 0; $i < $byte; $i++) {
            fwrite($handle, "a");
        }
        fclose($handle);

        // ログ書き出し
        $configPath = $this->getLogConfigPath() . "/" . $configPath;
        $this->write("INFO", $configPath, "test");

        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $now_date = $created_at_date = date("YmdHis", $now);
        // ローテートファイル名
        $rotatedLogPath = $this->getRoot() . $this->getSampleAppPath() . "/log/webstream.test.${created_at_date}-${now_date}.log";
        // ローテートされていればローテートしたログファイルが存在する
        $this->assertFileExists($rotatedLogPath);
        // ローテートしたログファイルを削除
        if (file_exists($rotatedLogPath)) {
            unlink($rotatedLogPath);
        }
    }

    /**
     * 正常系
     * ログ設定ファイルにローテート設定(サイズ単位)されていて、現在のログサイズが
     * 指定値より小さい場合、ログローテートが実行されないこと
     * @test
     * @dataProvider rotateSizeWithinProvider
     */
    public function okRotateSizeWithin($configPath, $byte)
    {
        // ログファイルに1023バイト以下のデータを書き込む
        $logPath = $this->getRoot() . "/" . $this->getLogFilePath();
        $handle = fopen($logPath, "w");
        for ($i = 0; $i < $byte; $i++) {
            fwrite($handle, "a");
        }
        fclose($handle);

        // ログ書き出し
        $configPath = $this->getLogConfigPath() . "/" . $configPath;
        $this->write("INFO", $configPath, "test");

        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $now_date = $created_at_date = date("YmdHis", $now);
        // ローテートファイル名
        $rotatedLogPath = $this->getRoot() . $this->getSampleAppPath() . "/log/webstream.test.${created_at_date}-${now_date}.log";
        // ローテートされたかチェック
        $this->assertFalse(file_exists($rotatedLogPath));
    }

    /**
     * 異常系
     * Loggerを初期化していない場合、例外が発生すること
     * @test
     * @expectedException WebStream\Exception\LoggerException
     * @expectedExceptionMessage Logger is not initialized.
     */
    public function ngNotInitialized()
    {
        Logger::info("test");
    }

    /**
     * 異常系
     * ログ設定ファイルが存在しない場合、例外が発生すること
     * @test
     * @expectedException WebStream\Exception\LoggerException
     * @expectedExceptionMessage Log config file does not exist: dummy.ini
     */
    public function ngConfigFileNotFound()
    {
        Logger::init("dummy.ini");
        $this->assertTrue(false);
    }

    /**
     * 異常系
     * ログ設定ファイルのログファイルパスが存在しない場合、例外が発生すること
     * @test
     * @expectedException WebStream\Exception\LoggerException
     */
    public function ngInvalidConfigPath()
    {
        $comfigPath = $this->getLogConfigPath() . "/log.test.ng1.ini";
        Logger::init($comfigPath);
        $this->assertTrue(false);
    }

    /**
     * 異常系
     * ログ設定ファイルのログレベルが不正な場合、例外が発生すること
     * @test
     * @expectedException WebStream\Exception\LoggerException
     */
    public function ngInvalidLogLevel()
    {
        $comfigPath = $this->getLogConfigPath() . "/log.test.ng2.ini";
        Logger::init($comfigPath);
        $this->assertTrue(false);
    }

    /**
     * 異常系
     * ログの書き込み権限がない場合、例外が発生すること
     * @test
     * @expectedException WebStream\Exception\LoggerException
     */
    public function ngNotPermittedWriteLog()
    {
        $comfigPath = $this->getLogConfigPath() . "/log.test.ng3.ini";
        Logger::init($comfigPath);
        Logger::info("test");
        $this->assertTrue(false);
    }

    /**
     * 異常系
     * ログ設定ファイルにローテート設定(時間単位)が指定されない場合、
     * ステータスファイルが作成されないこと
     * @test
     * @dataProvider notFoundRotateCycleConfigProvider
     */
    public function ngNotFoundRotateCycleConfig($configPath)
    {
        $statusPath = $this->getRoot() . $this->getSampleAppPath() . "/log/stream.status";
        if (file_exists($statusPath)) {
            unlink($statusPath);
        }
        $configPath = $this->getLogConfigPath() . "/" . $configPath;
        Logger::init($configPath);
        Logger::info("test");
        $this->assertFalse(file_exists($statusPath));
    }

    /**
     * 異常系
     * ログ設定ファイルのローテート設定(時間単位)が間違っている場合、
     * 例外が発生すること
     * @test
     * @dataProvider invalidRotateCycleConfigProvider
     * @expectedException WebStream\Exception\LoggerException
     * @expectedExceptionMessage Invalid log rotate cycle: dummy
     */
    public function ngInvalidRotateCycleConfig($configPath)
    {
        $configPath = $this->getLogConfigPath() . "/" . $configPath;
        Logger::init($configPath);
        Logger::info("test");
    }

    /**
     * 異常系
     * ログ設定ファイルにローテート設定(サイズ単位)が指定されない場合、
     * ステータスファイルが作成されないこと
     * @test
     * @dataProvider notFoundRotateSizeConfigProvider
     */
    public function ngNotFoundRotateSizeConfig($configPath)
    {
        $statusPath = $this->getRoot() . $this->getSampleAppPath() . "/log/stream.status";
        if (file_exists($statusPath)) {
            unlink($statusPath);
        }
        $configPath = $this->getLogConfigPath() . "/" . $configPath;
        Logger::init($configPath);
        Logger::info("test");
        $this->assertFalse(file_exists($statusPath));
    }

    /**
     * 異常系
     * ログ設定ファイルのローテート設定(サイズ単位)が間違っている場合、
     * 例外が発生すること
     * @test
     * @dataProvider invalidRotateSizeConfigProvider
     * @expectedException WebStream\Exception\LoggerException
     * @expectedExceptionMessage Invalid log rotate size: dummy
     */
    public function ngInvalidRotateSizeConfig($configPath)
    {
        $configPath = $this->getLogConfigPath() . "/" . $configPath;
        Logger::init($configPath);
        Logger::info("test");
    }
}
