<?php
namespace WebStream\Log\Test\Providers;

/**
 * LoggerProvider
 * @author Ryuichi TANAKA.
 * @since 2016/01/30
 * @version 0.7
 */
trait LoggerProvider
{
    public function loggerAdapterProvider()
    {
        return [
            ["debug"],
            ["info"],
            ["notice"],
            ["warn"],
            ["warning"],
            ["error"],
            ["critical"],
            ["alert"],
            ["emergency"],
            ["fatal"]
        ];
    }

    public function loggerAdapterWithPlaceholderProvider()
    {
        return [
            ["debug", "log message for debug.", "log message for { level }.", ["level" => "debug"]],
            ["debug", "log message for debug.", "log message for {level }.", ["level" => "debug"]],
            ["debug", "log message for debug.", "log message for { level}.", ["level" => "debug"]],
            ["debug", "log message for debug.", "log message for {level}.", ["level" => "debug"]]
        ];
    }

    public function logLevelDebugProvider()
    {
        return [
            ["debug", true],
            ["info", true],
            ["notice", true],
            ["warn", true],
            ["warning", true],
            ["error", true],
            ["critical", true],
            ["alert", true],
            ["emergency", true],
            ["fatal", true]
        ];
    }

    public function logLevelInfoProvider()
    {
        return [
            ["debug", false],
            ["info", true],
            ["notice", true],
            ["warn", true],
            ["warning", true],
            ["error", true],
            ["critical", true],
            ["alert", true],
            ["emergency", true],
            ["fatal", true]
        ];
    }

    public function logLevelNoticeProvider()
    {
        return [
            ["debug", false],
            ["info", false],
            ["notice", true],
            ["warn", true],
            ["warning", true],
            ["error", true],
            ["critical", true],
            ["alert", true],
            ["emergency", true],
            ["fatal", true]
        ];
    }

    public function logLevelWarnProvider()
    {
        return [
            ["debug", false],
            ["info", false],
            ["notice", false],
            ["warn", true],
            ["warning", true],
            ["error", true],
            ["critical", true],
            ["alert", true],
            ["emergency", true],
            ["fatal", true]
        ];
    }

    public function logLevelWarningProvider()
    {
        return [
            ["debug", false],
            ["info", false],
            ["notice", false],
            ["warn", true],
            ["warning", true],
            ["error", true],
            ["critical", true],
            ["alert", true],
            ["emergency", true],
            ["fatal", true]
        ];
    }

    public function logLevelErrorProvider()
    {
        return [
            ["debug", false],
            ["info", false],
            ["notice", false],
            ["warn", false],
            ["warning", false],
            ["error", true],
            ["critical", true],
            ["alert", true],
            ["emergency", true],
            ["fatal", true]
        ];
    }

    public function logLevelCriticalProvider()
    {
        return [
            ["debug", false],
            ["info", false],
            ["notice", false],
            ["warn", false],
            ["warning", false],
            ["error", false],
            ["critical", true],
            ["alert", true],
            ["emergency", true],
            ["fatal", true]
        ];
    }

    public function logLevelAlertProvider()
    {
        return [
            ["debug", false],
            ["info", false],
            ["notice", false],
            ["warn", false],
            ["warning", false],
            ["error", false],
            ["critical", false],
            ["alert", true],
            ["emergency", true],
            ["fatal", true]
        ];
    }

    public function logLevelEmergencyProvider()
    {
        return [
            ["debug", false],
            ["info", false],
            ["notice", false],
            ["warn", false],
            ["warning", false],
            ["error", false],
            ["critical", false],
            ["alert", false],
            ["emergency", true],
            ["fatal", true]
        ];
    }

    public function logLevelFatalProvider()
    {
        return [
            ["debug", false],
            ["info", false],
            ["notice", false],
            ["warn", false],
            ["warning", false],
            ["error", false],
            ["critical", false],
            ["alert", false],
            ["emergency", false],
            ["fatal", true]
        ];
    }

    public function loggerFormatterProvider()
    {
        return [
            ["log.test3_1.ini", "message", "message"],
            ["log.test3_2.ini", "message", "[debug] message"],
            ["log.test3_3.ini", "message", "[DEBUG] message"],
            ["log.test3_4.ini", "message", "[debug     ] message"],
            ["log.test3_5.ini", "message", "[DEBUG     ] message"],
            ["log.test3_6.ini", "message", "[webstream.logtest] message"]
        ];
    }

    public function loggerFormatterDateTimeProvider()
    {
        return [
            ["log.test4_1.ini", "/(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2})/", "message", "message"],
            ["log.test4_2.ini", "/(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}\.\d{3})/", "message", "message"],
            ["log.test4_3.ini", "/(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2})/", "message", "           message"],
            ["log.test4_4.ini", "/(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}\.\d{3})/", "message", "       message"]
        ];
    }

    public function writeTimingProvider()
    {
        return [
            [true, "b", "a", "a", "a".PHP_EOL."b".PHP_EOL."a".PHP_EOL],
            [false,"b", "a", "a", "b".PHP_EOL."a".PHP_EOL."a".PHP_EOL]
        ];
    }

    public function unRotateByCycleProvider()
    {
        $day_of_year = 24 * 365;
        $year = date("Y");
        if (($year % 4 === 0 && $year % 100 !== 0) || $year % 400 === 0) {
            $day_of_year = 24 * 366;
        }

        return [
            ["log.test6.day.ini", 1],
            ["log.test6.day.ini", 23],
            ["log.test6.week.ini", 24],
            ["log.test6.week.ini", 24 * 7 - 1],
            ["log.test6.month.ini", 24 * intval(date("t", time())) - 1],
            ["log.test6.year.ini", $day_of_year - 1]
        ];
    }

    public function rotateByCycleProvider()
    {
        $day_of_month = intval(date("t", time()));
        $day_of_year = 24 * 365;
        $year = date("Y");
        if (($year % 4 === 0 && $year % 100 !== 0) || $year % 400 === 0) {
            $day_of_year = 24 * 366;
        }

        return [
            ["log.test6.day.ini", 24],
            ["log.test6.day.ini", 25],
            ["log.test6.week.ini", 24 * 7],
            ["log.test6.week.ini", 24 * 7 + 1],
            ["log.test6.month.ini", 24 * $day_of_month],
            ["log.test6.month.ini", 24 * $day_of_month + 1],
            ["log.test6.year.ini", $day_of_year],
            ["log.test6.year.ini", $day_of_year + 1]
        ];
    }
}
