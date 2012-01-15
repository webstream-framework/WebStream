<?php
/**
 * Loggerクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/08/25
 */
require_once 'UnitTestBase.php';
 
class LoggerTest extends UnitTestBase {
    private $logger;
    private $logfile;
    
    public function setUp() {
        parent::setUp();
        // ログ出力ディレクトリ、ログレベルをテスト用に変更
        $class = new ReflectionClass("Logger");
        $property = $class->getProperty("log_dir");
        $property->setAccessible(true);
        $property->setValue($class, $this->log_dir);
        $property = $class->getProperty("level");
        $property->setAccessible(true);
        $property->setValue($class, 1);
        $this->logger = $class;
        // ログファイルパスを取得
        $property = $class->getProperty("log_filename");
        $property->setAccessible(true);
        $log_filename = $property->getValue();
        $this->logfile = Utility::getRoot() . '/' . $this->log_dir . '/' . $log_filename;
    }
    
    public function tearDown() {
        //unlink($this->logfile);
    }
 
    /**
     * 正常系
     * エラーメッセージのみ指定された場合、ログレベルが「debug」のログを書き出せること
     * @dataProvider writeDebugProvider
     */
    public function testOkWriteDebug($msg) {
        $method = $this->logger->getMethod("debug");
        $method->invoke(null, $msg);
        $file = file($this->logfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $line_tail = array_pop($file);
        
        if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},\d{1,2}\]\s\[(.+?)\]\s(.*)$/',
                       $line_tail, $matches)) {
            $target = array("DEBUG", $msg);
            $result = array($matches[1], $matches[2]);
            $this->assertEquals($target, $result);
        }
        else {
            $this->assertTrue(false);
        }
    }
    
    /**
     * 正常系
     * エラーメッセージ、スタックトレースが指定された場合、ログレベルが「debug」のログを書き出せること
     * @dataProvider writeDebugWithStackTraceProvider
     */
    public function testOkWriteDebugWithStackTrace($msg, $stacktrace = null) {
        $method = $this->logger->getMethod("debug");
        $method->invoke(null, $msg, $stacktrace);
        $file = file($this->logfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $line_tail = array_pop($file);
        
        if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},\d{1,2}\]\s\[(.+?)\]\s(.*)\s-\s(.*)$/',
                       $line_tail, $matches)) {
            $target = array("DEBUG", $msg, preg_replace('/^#0\s/', '', $stacktrace));
            $result = array($matches[1], $matches[2], $matches[3]);
            $this->assertEquals($target, $result);
        }
        else {
            $this->assertTrue(false);
        }
    }
    
    /**
     * 正常系
     * エラーメッセージのみ指定された場合、ログレベルが「info」のログを書き出せること
     * @dataProvider writeInfoProvider
     */
    public function testOkWriteInfo($msg, $stacktrace = null) {
        $method = $this->logger->getMethod("info");
        $method->invoke(null, $msg);
        $file = file($this->logfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $line_tail = array_pop($file);
        
        if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},\d{1,2}\]\s\[(.+?)\]\s(.*)$/',
                       $line_tail, $matches)) {
            $target = array("INFO", $msg);
            $result = array($matches[1], $matches[2]);
            $this->assertEquals($target, $result);
        }
        else {
            $this->assertTrue(false);
        }
    }

    /**
     * 正常系
     * エラーメッセージ、スタックトレースが指定された場合、ログレベルが「debug」のログを書き出せること
     * @dataProvider writeInfoWithStackTraceProvider
     */
    public function testOkWriteInfoWithStackTrace($msg, $stacktrace = null) {
        $method = $this->logger->getMethod("info");
        $method->invoke(null, $msg, $stacktrace);
        $file = file($this->logfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $line_tail = array_pop($file);
        
        if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},\d{1,2}\]\s\[(.+?)\]\s(.*)\s-\s(.*)$/',
                       $line_tail, $matches)) {
            $target = array("INFO", $msg, preg_replace('/^#0\s/', '', $stacktrace));
            $result = array($matches[1], $matches[2], $matches[3]);
            $this->assertEquals($target, $result);
        }
        else {
            $this->assertTrue(false);
        }
    }

    /**
     * 正常系
     * エラーメッセージのみ指定された場合、ログレベルが「warn」のログを書き出せること
     * @dataProvider writeWarnProvider
     */
    public function testOkWriteWarn($msg, $stacktrace = null) {
        $method = $this->logger->getMethod("warn");
        $method->invoke(null, $msg);
        $file = file($this->logfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $line_tail = array_pop($file);
        
        if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},\d{1,2}\]\s\[(.+?)\]\s(.*)$/',
                       $line_tail, $matches)) {
            $target = array("WARN", $msg);
            $result = array($matches[1], $matches[2]);
            $this->assertEquals($target, $result);
        }
        else {
            $this->assertTrue(false);
        }
    }

    /**
     * 正常系
     * エラーメッセージ、スタックトレースが指定された場合、ログレベルが「warn」のログを書き出せること
     * @dataProvider writeWarnWithStackTraceProvider
     */
    public function testOkWriteWarnWithStackTrace($msg, $stacktrace = null) {
        $method = $this->logger->getMethod("warn");
        $method->invoke(null, $msg, $stacktrace);
        $file = file($this->logfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $line_tail = array_pop($file);
        
        if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},\d{1,2}\]\s\[(.+?)\]\s(.*)\s-\s(.*)$/',
                       $line_tail, $matches)) {
            $target = array("WARN", $msg, preg_replace('/^#0\s/', '', $stacktrace));
            $result = array($matches[1], $matches[2], $matches[3]);
            $this->assertEquals($target, $result);
        }
        else {
            $this->assertTrue(false);
        }
    }

    /**
     * 正常系
     * エラーメッセージのみ指定された場合、ログレベルが「error」のログを書き出せること
     * @dataProvider writeErrorProvider
     */
    public function testOkWriteError($msg, $stacktrace = null) {
        $method = $this->logger->getMethod("error");
        $method->invoke(null, $msg);
        $file = file($this->logfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $line_tail = array_pop($file);
        
        if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},\d{1,2}\]\s\[(.+?)\]\s(.*)$/',
                       $line_tail, $matches)) {
            $target = array("ERROR", $msg);
            $result = array($matches[1], $matches[2]);
            $this->assertEquals($target, $result);
        }
        else {
            $this->assertTrue(false);
        }
    }

    /**
     * 正常系
     * エラーメッセージ、スタックトレースが指定された場合、ログレベルが「error」のログを書き出せること
     * @dataProvider writeErrorWithStackTraceProvider
     */
    public function testOkWriteErrorWithStackTrace($msg, $stacktrace = null) {
        $method = $this->logger->getMethod("error");
        $method->invoke(null, $msg, $stacktrace);
        $file = file($this->logfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $line_tail = array_pop($file);
        
        if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},\d{1,2}\]\s\[(.+?)\]\s(.*)\s-\s(.*)$/',
                       $line_tail, $matches)) {
            $target = array("ERROR", $msg, preg_replace('/^#0\s/', '', $stacktrace));
            $result = array($matches[1], $matches[2], $matches[3]);
            $this->assertEquals($target, $result);
        }
        else {
            $this->assertTrue(false);
        }
    }

    /**
     * 正常系
     * エラーメッセージのみ指定された場合、ログレベルが「fatal」のログを書き出せること
     * @dataProvider writeFatalProvider
     */
    public function testOkWriteFatal($msg, $stacktrace = null) {
        $method = $this->logger->getMethod("fatal");
        $method->invoke(null, $msg);
        $file = file($this->logfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $line_tail = array_pop($file);
        
        if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},\d{1,2}\]\s\[(.+?)\]\s(.*)$/',
                       $line_tail, $matches)) {
            $target = array("FATAL", $msg);
            $result = array($matches[1], $matches[2]);
            $this->assertEquals($target, $result);
        }
        else {
            $this->assertTrue(false);
        }
    }

    /**
     * 正常系
     * エラーメッセージ、スタックトレースが指定された場合、ログレベルが「fatal」のログを書き出せること
     * @dataProvider writeFatalWithStackTraceProvider
     */
    public function testOkWriteFatalWithStackTrace($msg, $stacktrace = null) {
        $method = $this->logger->getMethod("fatal");
        $method->invoke(null, $msg, $stacktrace);
        $file = file($this->logfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $line_tail = array_pop($file);
        
        if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},\d{1,2}\]\s\[(.+?)\]\s(.*)\s-\s(.*)$/',
                       $line_tail, $matches)) {
            $target = array("FATAL", $msg, preg_replace('/^#0\s/', '', $stacktrace));
            $result = array($matches[1], $matches[2], $matches[3]);
            $this->assertEquals($target, $result);
        }
        else {
            $this->assertTrue(false);
        }
    }
}