<?php
/**
 * Loggerクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/08/25
 */
require_once 'UnitTestBase.php';
 
class LoggerTest extends UnitTestBase {
    
    public function setUp() {
        parent::setUp();
    }
    
    public function tearDown() {
        //unlink($this->logfile);
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
            if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},.{2}\]\s\[(.+?)\]\s(.*)$/',
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
            if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},.{2}\]\s\[(.+?)\]\s(.*)\s-\s(.*)$/',
                   $line_tail, $matches)) {
                $target = array($level, $msg, preg_replace('/^#0\s/', '', $stacktrace));
                $result = array($matches[1], $matches[2], $matches[3]);
                $this->assertEquals($target, $result);
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
        
        if ($stacktrace === null) {
            if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},.{2}\]\s\[(.+?)\]\s(.*)$/',
                    $line_tail, $matches)) {
                if ($level === "DEBUG") {
                    $this->assertNotEquals($msg, $matches[2]);
                }
                else {
                    $target = array($level, $msg);
                    $result = array($matches[1], $matches[2]);
                    $this->assertEquals($target, $result);
                }
            }
            else {
                $this->assertTrue(false);
            }
        }
        else {
            if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},.{2}\]\s\[(.+?)\]\s(.*)\s-\s(.*)$/',
                   $line_tail, $matches)) {
                if ($level === "DEBUG") {
                    $this->assertNotEquals($msg, $matches[2]);
                }
                else {
                    $target = array($level, $msg, preg_replace('/^#0\s/', '', $stacktrace));
                    $result = array($matches[1], $matches[2], $matches[3]);
                    $this->assertEquals($target, $result);
                }
            }
            else {
                $this->assertTrue(false);
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
        
        if ($stacktrace === null) {
            if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},.{2}\]\s\[(.+?)\]\s(.*)$/',
                    $line_tail, $matches)) {
                if ($level === "DEBUG" || $level === "INFO") {
                    $this->assertNotEquals($msg, $matches[2]);
                }
                else {
                    $target = array($level, $msg);
                    $result = array($matches[1], $matches[2]);
                    $this->assertEquals($target, $result);
                }
            }
            else {
                $this->assertTrue(false);
            }
        }
        else {
            if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},.{2}\]\s\[(.+?)\]\s(.*)\s-\s(.*)$/',
                   $line_tail, $matches)) {
                if ($level === "DEBUG" || $level === "INFO") {
                    $this->assertNotEquals($msg, $matches[2]);
                }
                else {
                    $target = array($level, $msg, preg_replace('/^#0\s/', '', $stacktrace));
                    $result = array($matches[1], $matches[2], $matches[3]);
                    $this->assertEquals($target, $result);
                }
            }
            else {
                $this->assertTrue(false);
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
        
        if ($stacktrace === null) {
            if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},.{2}\]\s\[(.+?)\]\s(.*)$/',
                    $line_tail, $matches)) {
                if ($level === "DEBUG" || $level === "INFO" || $level === "WARN") {
                    $this->assertNotEquals($msg, $matches[2]);
                }
                else {
                    $target = array($level, $msg);
                    $result = array($matches[1], $matches[2]);
                    $this->assertEquals($target, $result);
                }
            }
            else {
                $this->assertTrue(false);
            }
        }
        else {
            if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},.{2}\]\s\[(.+?)\]\s(.*)\s-\s(.*)$/',
                   $line_tail, $matches)) {
                if ($level === "DEBUG" || $level === "INFO" || $level === "WARN") {
                    $this->assertNotEquals($msg, $matches[2]);
                }
                else {
                    $target = array($level, $msg, preg_replace('/^#0\s/', '', $stacktrace));
                    $result = array($matches[1], $matches[2], $matches[3]);
                    $this->assertEquals($target, $result);
                }
            }
            else {
                $this->assertTrue(false);
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
        
        if ($stacktrace === null) {
            if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},.{2}\]\s\[(.+?)\]\s(.*)$/',
                    $line_tail, $matches)) {
                if ($level === "DEBUG" || $level === "INFO" || $level === "WARN" || $level === "ERROR") {
                    $this->assertNotEquals($msg, $matches[2]);
                }
                else {
                    $target = array($level, $msg);
                    $result = array($matches[1], $matches[2]);
                    $this->assertEquals($target, $result);
                }
            }
            else {
                $this->assertTrue(false);
            }
        }
        else {
            if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},.{2}\]\s\[(.+?)\]\s(.*)\s-\s(.*)$/',
                   $line_tail, $matches)) {
                if ($level === "DEBUG" || $level === "INFO" || $level === "WARN" || $level === "ERROR") {
                    $this->assertNotEquals($msg, $matches[2]);
                }
                else {
                    $target = array($level, $msg, preg_replace('/^#0\s/', '', $stacktrace));
                    $result = array($matches[1], $matches[2], $matches[3]);
                    $this->assertEquals($target, $result);
                }
            }
            else {
                $this->assertTrue(false);
            }
        }
    }

    /**
     * 異常系
     * ログ設定ファイルが存在しない場合、例外が発生すること
     * @expectedException LoggerException
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
     * @expectedException LoggerException
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
     * @expectedException LoggerException
     * @expectedExceptionMessage Invalid log level: dummy
     */
    public function testNgInvalidLogLevel() {
        $comfig_path = $this->config_path_log . "log.test.ng2.ini";
        Logger::init($comfig_path);
        $this->assertTrue(false);
    }
}