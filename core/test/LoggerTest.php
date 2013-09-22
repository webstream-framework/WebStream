<?php
namespace WebStream\Test;
use WebStream\Logger;
use WebStream\Utility;
/**
 * Loggerクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/08/25
 */
require_once 'UnitTestBase.php';

class LoggerTest extends UnitTestBase {

    public function setUp() {
        $this->loadModule();
    }

    public function tearDown() {
        $log_path = Utility::getRoot() . $this->testdata_dir . "/log/stream.log";
        $handle = fopen($log_path, "w+");
        fclose($handle);
        chmod($log_path, 0777);
    }

    private function write($level, $config_path, $msg, $stacktrace = null) {
        Logger::init($config_path);
        if ($level === "DEBUG") {
            Logger::debug($msg, $stacktrace);
        }
        else if ($level === "INFO") {
            Logger::info($msg, $stacktrace);
        }
        else if ($level === "WARN") {
            Logger::warn($msg, $stacktrace);
        }
        else if ($level === "ERROR") {
            Logger::error($msg, $stacktrace);
        }
        else if ($level === "FATAL") {
            Logger::fatal($msg, $stacktrace);
        }
    }

    /**
     * 正常系
     * ログレベルが「debug」のとき、
     * 「debug」「info」「warn」「error」「fatal」レベルのログが書き出せること
     * @dataProvider logLevelDebugProvider
     */
    public function testOkWriteDebug($level, $config_path, $msg, $stacktrace = null) {
        $config_path = $this->config_path_log . $config_path;
        $this->write($level, $config_path, $msg, $stacktrace);
        $line_tail = $this->logTail($config_path);
        if ($stacktrace === null) {
            if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},.{1,2}\]\s\[(.+?)\]\s(.*)$/',
                   $line_tail, $matches)) {
                $target = array($level, $msg);
                $result = array($matches[1], $matches[2]);
                $this->assertEquals($target, $result);
            }
            else {
                $this->assertTrue(false);
            }
        }
        else {
            if (preg_match('/^(\t#\d.*)/', $line_tail, $matches)) {
                $this->assertEquals($line_tail, $matches[1]);
            }
            else {
                $this->assertTrue(false);
            }
        }
    }

    /**
     * 正常系
     * ログレベルが「info」のとき、
     * 「info」「warn」「error」「fatal」レベルのログが書き出せること
     * @dataProvider logLevelInfoProvider
     */
    public function testOkWriteInfo($level, $config_path, $msg, $stacktrace = null) {
        $config_path = $this->config_path_log . $config_path;
        $this->write($level, $config_path, $msg, $stacktrace);
        $line_tail = $this->logTail($config_path);

        if ($level === "DEBUG") {
            $this->assertNull($line_tail);
        }
        else {
            if ($stacktrace === null) {
                if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},.{1,2}\]\s\[(.+?)\]\s(.*)$/',
                        $line_tail, $matches)) {
                    $target = array($level, $msg);
                    $result = array($matches[1], $matches[2]);
                    $this->assertEquals($target, $result);
                }
                else {
                    $this->assertTrue(false);
                }
            }
            else {
                if (preg_match('/^(\t#\d.*)/', $line_tail, $matches)) {
                    $this->assertEquals($line_tail, $matches[1]);
                }
                else {
                    $this->assertTrue(false);
                }
            }
        }
    }

    /**
     * 正常系
     * ログレベルが「warn」のとき、
     * 「warn」「error」「fatal」レベルのログが書き出せること
     * @dataProvider logLevelWarnProvider
     */
    public function testOkWriteWarn($level, $config_path, $msg, $stacktrace = null) {
        $config_path = $this->config_path_log . $config_path;
        $this->write($level, $config_path, $msg, $stacktrace);
        $line_tail = $this->logTail($config_path);

        if ($level === "DEBUG" || $level === "INFO") {
            $this->assertNull($line_tail);
        }
        else {
            if ($stacktrace === null) {
                if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},.{1,2}\]\s\[(.+?)\]\s(.*)$/',
                        $line_tail, $matches)) {
                    $target = array($level, $msg);
                    $result = array($matches[1], $matches[2]);
                    $this->assertEquals($target, $result);
                }
                else {
                    $this->assertTrue(false);
                }
            }
            else {
                if (preg_match('/^(\t#\d.*)/', $line_tail, $matches)) {
                    $this->assertEquals($line_tail, $matches[1]);
                }
                else {
                    $this->assertTrue(false);
                }
            }
        }
    }

    /**
     * 正常系
     * ログレベルが「error」のとき、
     * 「error」「fatal」レベルのログが書き出せること
     * @dataProvider logLevelErrorProvider
     */
    public function testOkWriteError($level, $config_path, $msg, $stacktrace = null) {
        $config_path = $this->config_path_log . $config_path;
        $this->write($level, $config_path, $msg, $stacktrace);
        $line_tail = $this->logTail($config_path);

        if ($level === "DEBUG" || $level === "INFO" || $level === "WARN") {
            $this->assertNull($line_tail);
        }
        else {
            if ($stacktrace === null) {
                if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},.{1,2}\]\s\[(.+?)\]\s(.*)$/',
                        $line_tail, $matches)) {
                    $target = array($level, $msg);
                    $result = array($matches[1], $matches[2]);
                    $this->assertEquals($target, $result);
                }
                else {
                    $this->assertTrue(false);
                }
            }
            else {
                if (preg_match('/^(\t#\d.*)/', $line_tail, $matches)) {
                    $this->assertEquals($line_tail, $matches[1]);
                }
                else {
                    $this->assertTrue(false);
                }
            }
        }
    }

    /**
     * 正常系
     * ログレベルが「fatal」のとき、
     * 「fatal」レベルのログが書き出せること
     * @dataProvider logLevelFatalProvider
     */
    public function testOkWriteFatal($level, $config_path, $msg, $stacktrace = null) {
        $config_path = $this->config_path_log . $config_path;
        $this->write($level, $config_path, $msg, $stacktrace);
        $line_tail = $this->logTail($config_path);

        if ($level === "DEBUG" || $level === "INFO" || $level === "WARN" || $level === "ERROR") {
            $this->assertNull($line_tail);
        }
        else {
            if ($stacktrace === null) {
                if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},.{1,2}\]\s\[(.+?)\]\s(.*)$/',
                        $line_tail, $matches)) {
                    $target = array($level, $msg);
                    $result = array($matches[1], $matches[2]);
                    $this->assertEquals($target, $result);
                }
                else {
                    $this->assertTrue(false);
                }
            }
            else {
                if (preg_match('/^(\t#\d.*)/', $line_tail, $matches)) {
                    $this->assertEquals($line_tail, $matches[1]);
                }
                else {
                    $this->assertTrue(false);
                }
            }
        }
    }

    /**
     * 正常系
     * ログ設定ファイルにローテート設定が日単位かつログファイル作成日が24時間以内の場合、
     * ログローテートは実行されないこと
     * @dataProvider rotateCycleDayWithinProvider
     */
    public function testOkRotateCycleWithinDay($config_path, $hour) {
        // 既存のステータスファイルは削除
        $status_path = Utility::getRoot() . $this->testdata_dir . "/log/stream.status";
        if (file_exists($status_path)) unlink($status_path);
        // 現在時刻より$hour時間前のUnixTimeを取得
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $created_at = $now - 3600 * $hour;
        $created_at_date = date("YmdHis", $created_at);
        $now_date = date("YmdHis", $now);
        // ローテートファイル名(作成されないが)
        $rotated_log_path = Utility::getRoot() . $this->testdata_dir
             . "/log/stream.${created_at_date}-${now_date}.log";
        // テスト用のステータスファイルを作成
        file_put_contents($status_path, $created_at);
        // ログ書き出し
        $config_path = $this->config_path_log . $config_path;
        $this->write("INFO", $config_path, "test");
        // ローテートされたかチェック
        $this->assertFalse(file_exists($rotated_log_path));
    }

    /**
     * 正常系
     * ログ設定ファイルにローテート設定が日単位かつログファイル作成日が24時間以上の場合、
     * ログローテートが実行されること
     * @dataProvider rotateCycleDayProvider
     */
    public function testOkRotateCycleDay($config_path, $hour) {
        // 既存のステータスファイルは削除
        $status_path = Utility::getRoot() . $this->testdata_dir . "/log/stream.status";
        if (file_exists($status_path)) unlink($status_path);
        // 現在時刻より$hour時間前のUnixTimeを取得
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $created_at = $now - 3600 * $hour;
        $created_at_date = date("YmdHis", $created_at);
        $now_date = date("YmdHis", $now);
        // ローテートファイル名(作成されないが)
        $rotated_log_path = Utility::getRoot() . $this->testdata_dir
             . "/log/stream.${created_at_date}-${now_date}.log";
        // テスト用のステータスファイルを作成
        file_put_contents($status_path, $created_at);
        // ログ書き出し
        $config_path = $this->config_path_log . $config_path;
        $this->write("INFO", $config_path, "test");
        // ローテートされたかチェック
        $this->assertFileExists($rotated_log_path);
        // ローテートしたログファイルを削除
        if (file_exists($rotated_log_path)) {
            unlink($rotated_log_path);
        }
    }

   /**
     * 正常系
     * ログ設定ファイルにローテート設定が週単位かつログファイル作成日が1週間以内の場合、
     * ログローテートは実行されないこと
     * @dataProvider rotateCycleWeekWithinProvider
     */
    public function testOkRotateCycleWithinWeek($config_path, $hour) {
        // 既存のステータスファイルは削除
        $status_path = Utility::getRoot() . $this->testdata_dir . "/log/stream.status";
        if (file_exists($status_path)) unlink($status_path);
        // 現在時刻より$hour時間前のUnixTimeを取得
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $created_at = $now - 3600 * $hour;
        $created_at_date = date("YmdHis", $created_at);
        $now_date = date("YmdHis", $now);
        // ローテートファイル名(作成されないが)
        $rotated_log_path = Utility::getRoot() . $this->testdata_dir
             . "/log/stream.${created_at_date}-${now_date}.log";
        // テスト用のステータスファイルを作成
        file_put_contents($status_path, $created_at);
        // ログ書き出し
        $config_path = $this->config_path_log . $config_path;
        $this->write("INFO", $config_path, "test");
        // ローテートされたかチェック
        $this->assertFalse(file_exists($rotated_log_path));
    }

   /**
     * 正常系
     * ログ設定ファイルにローテート設定が週単位かつログファイル作成日が1週間以上の場合、
     * ログローテートが実行されること
     * @dataProvider rotateCycleWeekProvider
     */
    public function testOkRotateCycleWeek($config_path, $hour) {
        // 既存のステータスファイルは削除
        $status_path = Utility::getRoot() . $this->testdata_dir . "/log/stream.status";
        if (file_exists($status_path)) unlink($status_path);
        // 現在時刻より$hour時間前のUnixTimeを取得
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $created_at = $now - 3600 * $hour;
        $created_at_date = date("YmdHis", $created_at);
        $now_date = date("YmdHis", $now);
        // ローテートファイル名(作成されないが)
        $rotated_log_path = Utility::getRoot() . $this->testdata_dir
             . "/log/stream.${created_at_date}-${now_date}.log";
        // テスト用のステータスファイルを作成
        file_put_contents($status_path, $created_at);
        // ログ書き出し
        $config_path = $this->config_path_log . $config_path;
        $this->write("INFO", $config_path, "test");
        // ローテートされたかチェック
        $this->assertFileExists($rotated_log_path);
        // ローテートしたログファイルを削除
        if (file_exists($rotated_log_path)) {
            unlink($rotated_log_path);
        }
    }

   /**
     * 正常系
     * ログ設定ファイルにローテート設定が月単位かつログファイル作成日が1ヶ月以内の場合、
     * ログローテートは実行されないこと
     * @dataProvider rotateCycleMonthWithinProvider
     */
    public function testOkRotateCycleWithinMonth($config_path, $hour) {
        // 既存のステータスファイルは削除
        $status_path = Utility::getRoot() . $this->testdata_dir . "/log/stream.status";
        if (file_exists($status_path)) unlink($status_path);
        // 現在時刻より$hour時間前のUnixTimeを取得
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $created_at = $now - 3600 * $hour;
        $created_at_date = date("YmdHis", $created_at);
        $now_date = date("YmdHis", $now);
        // ローテートファイル名(作成されないが)
        $rotated_log_path = Utility::getRoot() . $this->testdata_dir
             . "/log/stream.${created_at_date}-${now_date}.log";
        // テスト用のステータスファイルを作成
        file_put_contents($status_path, $created_at);
        // ログ書き出し
        $config_path = $this->config_path_log . $config_path;
        $this->write("INFO", $config_path, "test");
        // ローテートされたかチェック
        $this->assertFalse(file_exists($rotated_log_path));
    }

   /**
     * 正常系
     * ログ設定ファイルにローテート設定が月単位かつログファイル作成日が1ヶ月以上の場合、
     * ログローテートが実行されること
     * @dataProvider rotateCycleMonthProvider
     */
    public function testOkRotateCycleMonth($config_path, $hour) {
        // 既存のステータスファイルは削除
        $status_path = Utility::getRoot() . $this->testdata_dir . "/log/stream.status";
        if (file_exists($status_path)) unlink($status_path);
        // 現在時刻より$hour時間前のUnixTimeを取得
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $created_at = $now - 3600 * $hour;
        $created_at_date = date("YmdHis", $created_at);
        $now_date = date("YmdHis", $now);
        // ローテートファイル名(作成されないが)
        $rotated_log_path = Utility::getRoot() . $this->testdata_dir
             . "/log/stream.${created_at_date}-${now_date}.log";
        // テスト用のステータスファイルを作成
        file_put_contents($status_path, $created_at);
        // ログ書き出し
        $config_path = $this->config_path_log . $config_path;
        $this->write("INFO", $config_path, "test");
        // ローテートされたかチェック
        $this->assertFileExists($rotated_log_path);
        // ローテートしたログファイルを削除
        if (file_exists($rotated_log_path)) {
            unlink($rotated_log_path);
        }
    }

   /**
     * 正常系
     * ログ設定ファイルにローテート設定が年単位かつログファイル作成日が1年以内の場合、
     * ログローテートは実行されないこと
     * @dataProvider rotateCycleYearWithinProvider
     */
    public function testOkRotateCycleWithinYear($config_path, $hour) {
        // 既存のステータスファイルは削除
        $status_path = Utility::getRoot() . $this->testdata_dir . "/log/stream.status";
        if (file_exists($status_path)) unlink($status_path);
        // 現在時刻より$hour時間前のUnixTimeを取得
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $created_at = $now - 3600 * $hour;
        $created_at_date = date("YmdHis", $created_at);
        $now_date = date("YmdHis", $now);
        // ローテートファイル名(作成されないが)
        $rotated_log_path = Utility::getRoot() . $this->testdata_dir
             . "/log/stream.${created_at_date}-${now_date}.log";
        // テスト用のステータスファイルを作成
        file_put_contents($status_path, $created_at);
        // ログ書き出し
        $config_path = $this->config_path_log . $config_path;
        $this->write("INFO", $config_path, "test");
        // ローテートされたかチェック
        $this->assertFalse(file_exists($rotated_log_path));
    }

   /**
     * 正常系
     * ログ設定ファイルにローテート設定が年単位かつログファイル作成日が1年以上の場合、
     * ログローテートが実行されること
     * @dataProvider rotateCycleYearProvider
     */
    public function testOkRotateCycleYear($config_path, $hour) {
        // 既存のステータスファイルは削除
        $status_path = Utility::getRoot() . $this->testdata_dir . "/log/stream.status";
        if (file_exists($status_path)) unlink($status_path);
        // 現在時刻より$hour時間前のUnixTimeを取得
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $created_at = $now - 3600 * $hour;
        $created_at_date = date("YmdHis", $created_at);
        $now_date = date("YmdHis", $now);
        // ローテートファイル名(作成されないが)
        $rotated_log_path = Utility::getRoot() . $this->testdata_dir
             . "/log/stream.${created_at_date}-${now_date}.log";
        // テスト用のステータスファイルを作成
        file_put_contents($status_path, $created_at);
        // ログ書き出し
        $config_path = $this->config_path_log . $config_path;
        $this->write("INFO", $config_path, "test");
        // ローテートされたかチェック
        $this->assertFileExists($rotated_log_path);
        // ローテートしたログファイルを削除
        if (file_exists($rotated_log_path)) {
            unlink($rotated_log_path);
        }
    }

    /**
     * 正常系
     * ログ設定ファイルにローテート設定(サイズ単位)されていて、現在のログサイズが
     * 指定値以上の場合、ログローテートが実行されること
     * @dataProvider rotateSizeProvider
     */
    public function testOkRotateSize($config_path, $byte) {
        // ログファイルに1024バイトのデータを書き込む
        $log_path = Utility::getRoot() . $this->testdata_dir . "/log/stream.log";
        $handle = fopen($log_path, "w");
        for ($i = 0; $i < $byte; $i++) {
            fwrite($handle, "a");
        }
        fclose($handle);

        // ログ書き出し
        $config_path = $this->config_path_log . $config_path;
        $this->write("INFO", $config_path, "test");

        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $now_date = $created_at_date = date("YmdHis", $now);
        // ローテートファイル名
        $rotated_log_path = Utility::getRoot() . $this->testdata_dir
             . "/log/stream.${created_at_date}-${now_date}.log";

        // ローテートされていればローテートしたログファイルが存在する
        $this->assertFileExists($rotated_log_path);
        // ローテートしたログファイルを削除
        if (file_exists($rotated_log_path)) {
            unlink($rotated_log_path);
        }
    }

    /**
     * 正常系
     * ログ設定ファイルにローテート設定(サイズ単位)されていて、現在のログサイズが
     * 指定値より小さい場合、ログローテートが実行されないこと
     * @dataProvider rotateSizeWithinProvider
     */
    public function testOkRotateSizeWithin($config_path, $byte) {
        // ログファイルに1023バイト以下のデータを書き込む
        $log_path = Utility::getRoot() . $this->testdata_dir . "/log/stream.log";
        $handle = fopen($log_path, "w");
        for ($i = 0; $i < $byte; $i++) {
            fwrite($handle, "a");
        }
        fclose($handle);

        // ログ書き出し
        $config_path = $this->config_path_log . $config_path;
        $this->write("INFO", $config_path, "test");

        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $now_date = $created_at_date = date("YmdHis", $now);
        // ローテートファイル名
        $rotated_log_path = Utility::getRoot() . $this->testdata_dir
             . "/log/stream.${created_at_date}-${now_date}.log";
        // ローテートされたかチェック
        $this->assertFalse(file_exists($rotated_log_path));
    }

    /**
     * 異常系
     * ログ設定ファイルが存在しない場合、例外が発生すること
     * @expectedException WebStream\LoggerException
     * @expectedExceptionMessage Log config file does not exist: dummy.ini
     */
    public function testNgConfigFileNotFound() {
        $comfig_path = $this->config_path_log . "log.test.ng1.ini";
        Logger::init("dummy.ini");
        $this->assertTrue(false);
    }

    /**
     * 異常系
     * ログ設定ファイルのログファイルパスが存在しない場合、例外が発生すること
     * @expectedException WebStream\LoggerException
     * @expectedExceptionMessage Log directory does not exist: dummy/stream.log
     */
    public function testNgInvalidConfigPath() {
        $comfig_path = $this->config_path_log . "log.test.ng1.ini";
        Logger::init($comfig_path);
        $this->assertTrue(false);
    }

    /**
     * 異常系
     * ログ設定ファイルのログレベルが不正な場合、例外が発生すること
     * @expectedException WebStream\LoggerException
     * @expectedExceptionMessage Invalid log level: dummy
     */
    public function testNgInvalidLogLevel() {
        $comfig_path = $this->config_path_log . "log.test.ng2.ini";
        Logger::init($comfig_path);
        $this->assertTrue(false);
    }

    /**
     * 異常系
     * ログの書き込み権限がない場合、例外が発生すること
     * @expectedException WebStream\LoggerException
     */
    public function testNgNotPermittedWriteLog() {
        $comfig_path = $this->config_path_log . "log.test.ng3.ini";
        Logger::init($comfig_path);
        Logger::info("test");
        $this->assertTrue(false);
    }

    /**
     * 異常系
     * ログ設定ファイルにローテート設定(時間単位)が指定されない場合、
     * ステータスファイルが作成されないこと
     * @dataProvider notFoundRotateCycleConfigProvider
     */
    public function testNgNotFoundRotateCycleConfig($config_path) {
        $status_path = Utility::getRoot() . $this->testdata_dir . "/stream.status";
        if (file_exists($status_path)) unlink($status_path);
        $config_path = $this->config_path_log . $config_path;
        $this->write("INFO", $config_path, "test");
        $this->assertFalse(file_exists($status_path));
    }

    /**
     * 異常系
     * ログ設定ファイルのローテート設定(時間単位)が間違っている場合、例外が発生すること
     * @dataProvider invalidRotateCycleConfigProvider
     * @expectedException WebStream\LoggerException
     * @expectedExceptionMessage Invalid log rotate cycle: dummy
     */
    public function testNgInvalidRotateCycleConfig($config_path) {
        $config_path = $this->config_path_log . $config_path;
        $this->write("INFO", $config_path, "test");
    }

    /**
     * 異常系
     * ログ設定ファイルにローテート設定(サイズ単位)が指定されない場合、
     * ステータスファイルが作成されないこと
     * @dataProvider notFoundRotateSizeConfigProvider
     */
    public function testNgNotFoundRotateSizeConfig($config_path) {
        $status_path = Utility::getRoot() . $this->testdata_dir . "/stream.status";
        if (file_exists($status_path)) unlink($status_path);
        $config_path = $this->config_path_log . $config_path;
        $this->write("INFO", $config_path, "test");
        $this->assertFalse(file_exists($status_path));
    }

    /**
     * 異常系
     * ログ設定ファイルのローテート設定(サイズ単位)が間違っている場合、例外が発生すること
     * @dataProvider invalidRotateSizeConfigProvider
     * @expectedException WebStream\LoggerException
     * @expectedExceptionMessage Invalid log rotate size: dummy
     */
    public function testNgInvalidRotateSizeConfig($config_path) {
        $config_path = $this->config_path_log . $config_path;
        $this->write("INFO", $config_path, "test");
    }
}