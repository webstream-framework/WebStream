<?php
namespace WebStream\Log\Test;

require_once dirname(__FILE__) . '/../LoggerUtils.php';
require_once dirname(__FILE__) . '/../Logger.php';
require_once dirname(__FILE__) . '/../LoggerAdapter.php';
require_once dirname(__FILE__) . '/../LoggerConfigurationManager.php';
require_once dirname(__FILE__) . '/../LoggerFormatter.php';
require_once dirname(__FILE__) . '/../LoggerCache.php';
require_once dirname(__FILE__) . '/../Outputter/IOutputter.php';
require_once dirname(__FILE__) . '/../Outputter/ILazyWriter.php';
require_once dirname(__FILE__) . '/../Outputter/FileOutputter.php';
require_once dirname(__FILE__) . '/../Outputter/ConsoleOutputter.php';
require_once dirname(__FILE__) . '/../Modules/Cache/Modules/DI/Injector.php';
require_once dirname(__FILE__) . '/../Modules/Cache/Driver/CacheDriverFactory.php';
require_once dirname(__FILE__) . '/../Modules/Cache/Driver/ICache.php';
require_once dirname(__FILE__) . '/../Modules/Cache/Driver/Apcu.php';
require_once dirname(__FILE__) . '/../Modules/Container/Container.php';
require_once dirname(__FILE__) . '/../Modules/Container/ValueProxy.php';
require_once dirname(__FILE__) . '/../Modules/IO/File.php';
require_once dirname(__FILE__) . '/../Modules/IO/InputStream.php';
require_once dirname(__FILE__) . '/../Modules/IO/OutputStream.php';
require_once dirname(__FILE__) . '/../Modules/IO/FileInputStream.php';
require_once dirname(__FILE__) . '/../Modules/IO/FileOutputStream.php';
require_once dirname(__FILE__) . '/../Modules/IO/Reader/InputStreamReader.php';
require_once dirname(__FILE__) . '/../Modules/IO/Reader/FileReader.php';
require_once dirname(__FILE__) . '/../Modules/IO/Writer/OutputStreamWriter.php';
require_once dirname(__FILE__) . '/../Modules/IO/Writer/FileWriter.php';
require_once dirname(__FILE__) . '/../Modules/IO/Writer/SimpleFileWriter.php';
require_once dirname(__FILE__) . '/Modules/IOException.php';
require_once dirname(__FILE__) . '/Providers/LoggerProvider.php';

use WebStream\Container\Container;
use WebStream\Cache\Driver\CacheDriverFactory;
use WebStream\IO\File;
use WebStream\IO\Writer\FileWriter;
use WebStream\Log\Logger;
use WebStream\Log\LoggerAdapter;
use WebStream\Log\LoggerConfigurationManager;
use WebStream\Log\LoggerCache;
use WebStream\Log\Outputter\FileOutputter;
use WebStream\Log\Outputter\ConsoleOutputter;
use WebStream\Log\Test\Providers\LoggerProvider;

/**
 * LoggerTest
 * @author Ryuichi TANAKA.
 * @since 2016/01/30
 */
class LoggerTest extends \PHPUnit\Framework\TestCase
{
    use LoggerProvider;

    private function getLogger(string $configPath)
    {
        $manager = new LoggerConfigurationManager($configPath);
        $manager->load();
        Logger::init($manager->getConfig());
        $instance = Logger::getInstance();
        $instance->setOutputter([new FileOutputter("/tmp/webstream.logtest.log"), new ConsoleOutputter()]);

        return new LoggerAdapter($instance);
    }

    private function getLotateLogger(string $configPath)
    {
        $manager = new LoggerConfigurationManager($configPath);
        $manager->load();
        Logger::init($manager->getConfig());
        $instance = Logger::getInstance();
        $instance->setOutputter([new FileOutputter("/tmp/webstream.logtest.log")]);

        return new LoggerAdapter($instance);
    }

    private function assertLog($level, $msg, $logLine)
    {
        if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}\..{3}\]\[(.+?)\]\[(.+?)]\s(.*)$/', $logLine, $matches)) {
            $target = ["webstream.logtest", $level, $msg];
            $result = [trim($matches[1]), trim($matches[2]), $matches[3]];
            $this->assertEquals($target, $result);
        } else {
            $this->assertTrue(false);
        }
    }

    /**
     * 正常系
     * LoggerAdapter経由でログが書き込めること
     * @test
     * @dataProvider loggerAdapterProvider
     */
    public function okLoggerAdapter($level)
    {
        $msg = "log message";
        $configPath = dirname(__FILE__) . "/Fixtures/log.test1.${level}.ini";
        $logger = $this->getLogger($configPath);

        ob_start();
        $logger->{$level}($msg);
        $actual = ob_get_clean();

        $this->assertLog(strtoupper($level), $msg, $actual);
    }

    /**
     * 正常系
     * LoggerAdapter経由でログが書き込めること、プレースホルダーで値を埋め込めること
     * @test
     * @dataProvider loggerAdapterWithPlaceholderProvider
     */
    public function okLoggerAdapterWithPlaceholder($level, $msg1, $msg2, array $placeholder)
    {
        $msg = "log message";
        $configPath = dirname(__FILE__) . "/Fixtures/log.test1.${level}.ini";
        $logger = $this->getLogger($configPath);

        ob_start();
        $logger->{$level}($msg2, $placeholder);
        $actual = ob_get_clean();

        $this->assertLog(strtoupper($level), $msg1, $actual);
    }

    /**
     * 正常系
     * ログレベルが「debug」のとき、
     * 「debug」「info」「notice」「warn」「warning」「error」「critical」「alert」「emergency」「fatal」レベルのログが書き出せること
     * @test
     * @dataProvider logLevelDebugProvider
     */
    public function okWriteDebug($level, $isWrite)
    {
        $execLevel = "debug";
        $msg = "log message";
        $configPath = dirname(__FILE__) . "/Fixtures/log.test2.${execLevel}.ini";
        $logger = $this->getLogger($configPath);

        ob_start();
        $logger->{$level}($msg);
        $actual = ob_get_clean();

        $this->assertEquals($isWrite, trim($actual) === $msg);
    }

    /**
     * 正常系
     * ログレベルが「info」のとき、
     * 「info」「notice」「warn」「warning」「error」「critical」「alert」「emergency」「fatal」レベルのログが書き出せること
     * @test
     * @dataProvider logLevelInfoProvider
     */
    public function okWriteInfo($level, $isWrite)
    {
        $execLevel = "info";
        $msg = "log message";
        $configPath = dirname(__FILE__) . "/Fixtures/log.test2.${execLevel}.ini";
        $logger = $this->getLogger($configPath);

        ob_start();
        $logger->{$level}($msg);
        $actual = ob_get_clean();

        $this->assertEquals($isWrite, trim($actual) === $msg);
    }

    /**
     * 正常系
     * ログレベルが「notice」のとき、
     * 「notice」「warn」「warning」「error」「critical」「alert」「emergency」「fatal」レベルのログが書き出せること
     * @test
     * @dataProvider logLevelNoticeProvider
     */
    public function okWriteNotice($level, $isWrite)
    {
        $execLevel = "notice";
        $msg = "log message";
        $configPath = dirname(__FILE__) . "/Fixtures/log.test2.${execLevel}.ini";
        $logger = $this->getLogger($configPath);

        ob_start();
        $logger->{$level}($msg);
        $actual = ob_get_clean();

        $this->assertEquals($isWrite, trim($actual) === $msg);
    }

    /**
     * 正常系
     * ログレベルが「warn」のとき、
     * 「warn」「warning」「error」「critical」「alert」「emergency」「fatal」レベルのログが書き出せること
     * @test
     * @dataProvider logLevelWarnProvider
     */
    public function okWriteWarn($level, $isWrite)
    {
        $execLevel = "warn";
        $msg = "log message";
        $configPath = dirname(__FILE__) . "/Fixtures/log.test2.${execLevel}.ini";
        $logger = $this->getLogger($configPath);

        ob_start();
        $logger->{$level}($msg);
        $actual = ob_get_clean();

        $this->assertEquals($isWrite, trim($actual) === $msg);
    }

    /**
     * 正常系
     * ログレベルが「warning」のとき、
     * 「warn」「warning」「error」「critical」「alert」「emergency」「fatal」レベルのログが書き出せること
     * @test
     * @dataProvider logLevelWarningProvider
     */
    public function okWriteWarning($level, $isWrite)
    {
        $execLevel = "warning";
        $msg = "log message";
        $configPath = dirname(__FILE__) . "/Fixtures/log.test2.${execLevel}.ini";
        $logger = $this->getLogger($configPath);

        ob_start();
        $logger->{$level}($msg);
        $actual = ob_get_clean();

        $this->assertEquals($isWrite, trim($actual) === $msg);
    }

    /**
     * 正常系
     * ログレベルが「error」のとき、
     * 「error」「critical」「alert」「emergency」「fatal」レベルのログが書き出せること
     * @test
     * @dataProvider logLevelErrorProvider
     */
    public function okWriteError($level, $isWrite)
    {
        $execLevel = "error";
        $msg = "log message";
        $configPath = dirname(__FILE__) . "/Fixtures/log.test2.${execLevel}.ini";
        $logger = $this->getLogger($configPath);

        ob_start();
        $logger->{$level}($msg);
        $actual = ob_get_clean();

        $this->assertEquals($isWrite, trim($actual) === $msg);
    }

    /**
     * 正常系
     * ログレベルが「critical」のとき、
     * 「critical」「alert」「emergency」「fatal」レベルのログが書き出せること
     * @test
     * @dataProvider logLevelCriticalProvider
     */
    public function okWriteCritical($level, $isWrite)
    {
        $execLevel = "critical";
        $msg = "log message";
        $configPath = dirname(__FILE__) . "/Fixtures/log.test2.${execLevel}.ini";
        $logger = $this->getLogger($configPath);

        ob_start();
        $logger->{$level}($msg);
        $actual = ob_get_clean();

        $this->assertEquals($isWrite, trim($actual) === $msg);
    }

    /**
     * 正常系
     * ログレベルが「alert」のとき、
     * 「alert」「emergency」「fatal」レベルのログが書き出せること
     * @test
     * @dataProvider logLevelAlertProvider
     */
    public function okWriteAlert($level, $isWrite)
    {
        $execLevel = "alert";
        $msg = "log message";
        $configPath = dirname(__FILE__) . "/Fixtures/log.test2.${execLevel}.ini";
        $logger = $this->getLogger($configPath);

        ob_start();
        $logger->{$level}($msg);
        $actual = ob_get_clean();

        $this->assertEquals($isWrite, trim($actual) === $msg);
    }

    /**
     * 正常系
     * ログレベルが「emergency」のとき、
     * 「emergency」「fatal」レベルのログが書き出せること
     * @test
     * @dataProvider logLevelEmergencyProvider
     */
    public function okWriteEmergency($level, $isWrite)
    {
        $execLevel = "emergency";
        $msg = "log message";
        $configPath = dirname(__FILE__) . "/Fixtures/log.test2.${execLevel}.ini";
        $logger = $this->getLogger($configPath);

        ob_start();
        $logger->{$level}($msg);
        $actual = ob_get_clean();

        $this->assertEquals($isWrite, trim($actual) === $msg);
    }

    /**
     * 正常系
     * ログレベルが「fatal」のとき、
     * 「fatal」レベルのログが書き出せること
     * @test
     * @dataProvider logLevelFatalProvider
     */
    public function okWriteFatal($level, $isWrite)
    {
        $execLevel = "fatal";
        $msg = "log message";
        $configPath = dirname(__FILE__) . "/Fixtures/log.test2.${execLevel}.ini";
        $logger = $this->getLogger($configPath);

        ob_start();
        $logger->{$level}($msg);
        $actual = ob_get_clean();

        $this->assertEquals($isWrite, trim($actual) === $msg);
    }

    /**
     * 正常系
     * 指定されたフォーマットでログ出力されること
     * @test
     * @dataProvider loggerFormatterProvider
     */
    public function okLoggerFormatter($configPath, $msg, $formattedMessage)
    {
        $configPath = dirname(__FILE__) . "/Fixtures/${configPath}";
        $logger = $this->getLogger($configPath);

        ob_start();
        $logger->debug($msg);
        $actual = ob_get_clean();

        $this->assertEquals(trim($actual), $formattedMessage);
    }

    /**
     * 正常系
     * DateTimeフォーマットでログ出力されること
     * @test
     * @dataProvider loggerFormatterDateTimeProvider
     */
    public function okLoggerDateTimeFormatter($configPath, $dateTimeRegexp, $message, $messageWithSpace)
    {
        $configPath = dirname(__FILE__) . "/Fixtures/${configPath}";
        $logger = $this->getLogger($configPath);

        ob_start();
        $logger->debug($message);
        $actual = ob_get_clean();

        preg_match($dateTimeRegexp, $actual, $matches);
        $this->assertEquals(trim($actual), $matches[1] . $messageWithSpace);
    }

    /**
     * 正常系
     * ログの書き出しタイミングを制御できること
     * @test
     * @dataProvider writeTimingProvider
     */
    public function okLoggerWriteTiming($isLazy, $msg1, $msg2, $msg3, $result)
    {
        $configPath = dirname(__FILE__) . "/Fixtures/log.test5.ini";
        $logger = $this->getLogger($configPath);
        $manager = new LoggerConfigurationManager($configPath);
        $manager->load();
        Logger::init($manager->getConfig());
        $instance = Logger::getInstance();

        $outputter = new ConsoleOutputter();
        if ($isLazy) {
            $outputter->enableLazyWrite();
        } else {
            $outputter->enableDirectWrite();
        }
        $instance->setOutputter([$outputter]);

        $logger = new LoggerAdapter($instance);
        ob_start();
        $logger->debug($msg1);
        echo $msg2 . PHP_EOL;
        $logger->debug($msg3);
        if ($isLazy) {
            $logger->enableDirectWrite(); // バッファをクリアする
        }
        $actual = ob_get_clean();

        $this->assertEquals($actual, $result);
    }

    /**
     * 正常系
     * ローテート設定が
     * 日単位かつログファイル作成日24時間以内の場合、
     * 週単位かつログファイル作成日が1週間以内の場合、
     * 月単位かつログファイル作成日が1ヶ月以内の場合、
     * 年単位かつログファイル作成日が1年以内の場合、
     * ログローテートは実行されないこと
     * @test
     * @dataProvider unRotateByCycleProvider
     */
    public function okUnRotateByCycle($configPath, $hour)
    {
        $message = "hoge";
        $configPath = dirname(__FILE__) . "/Fixtures/${configPath}";
        $logger = $this->getLotateLogger($configPath);

        // 現在時刻より$hour時間前のUnixTimeを取得
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $createdAt = $now - 3600 * $hour;
        $createdAtDate = date("YmdHis", $createdAt);
        $nowDate = date("YmdHis", $now);

        $writer = new FileWriter("/tmp/webstream.logtest.status");
        $writer->write($createdAt);
        $writer->flush();
        $writer->close();
        $logger->info($message);

        $this->assertFileNotExists("/tmp/webstream.logtest.${createdAtDate}-${nowDate}.log");
    }

    /**
     * 正常系
     * ローテート設定が
     * 日単位かつログファイル作成日が24時間以上の場合、
     * 週単位かつログファイル作成日が1週間以上の場合、
     * 月単位かつログファイル作成日が1ヶ月以上の場合、
     * 年単位かつログファイル作成日が1年以上の場合、
     * ログローテートが実行されること
     * @test
     * @dataProvider rotateByCycleProvider
     */
    public function okRotateByCycle($configPath, $hour)
    {
        $message = "hoge";
        $configPath = dirname(__FILE__) . "/Fixtures/${configPath}";
        $logger = $this->getLotateLogger($configPath);

        // 現在時刻より$hour時間前のUnixTimeを取得
        $now = intval(preg_replace('/^.*\s/', '', microtime()));
        $createdAt = $now - 3600 * $hour;
        $createdAtDate = date("YmdHis", $createdAt);
        $nowDate = date("YmdHis", $now);

        $writer = new FileWriter("/tmp/webstream.logtest.status");
        $writer->write($createdAt);
        $writer->flush();
        $writer->close();
        $logger->info($message);

        $this->assertFileExists("/tmp/webstream.logtest.${createdAtDate}-${nowDate}.log");
    }

    /**
     * 正常系
     * ログキャッシュできること
     * @test
     */
    public function okLoggerCache()
    {
        $config = new Container(false);
        $config->classPrefix = "logger_cache";
        $config->cacheDir = "/tmp";
        $factory = new CacheDriverFactory();
        $driver = $factory->create("WebStream\Cache\Driver\Apcu", $config);
        $logger = new class() { function __call($name, $args) {} };
        $driver->inject('logger', $logger);
        $driver->clear();

        $cache = new LoggerCache($driver);
        $cache->add("a");
        $cache->add("b");
        $this->assertEquals(2, $cache->length());
        $this->assertEquals(implode("", $cache->get()), "ab");
    }

    /**
     * 正常系
     * ログファイルが見つからない場合、新規作成できること
     * @test
     */
    public function okLoggerNewLogFile()
    {
        $file = new File("/tmp/webstream.logtest.new.log");
        $file->delete();

        $configPath = dirname(__FILE__) . "/Fixtures/log.test7.ini";
        $manager = new LoggerConfigurationManager($configPath);
        $manager->load();
        Logger::init($manager->getConfig());
        $instance = Logger::getInstance();
        $instance->setOutputter([new FileOutputter($file->getFilePath())]);

        $logger = new LoggerAdapter($instance);
        $logger->debug("hoge");

        $this->assertFileExists($file->getFilePath());
    }
}
