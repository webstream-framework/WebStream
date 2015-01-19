<?php
namespace WebStream\Test\DataProvider;

/**
 * LogProvider
 * @author Ryuichi TANAKA.
 * @since 2013/09/07
 * @version 0.4
 */
trait LoggerProvider
{
    public function logLevelDebugProvider()
    {
        return [
            ["DEBUG", "log.test.debug.ok.ini", "log message for debug1."],
            ["DEBUG", "log.test.debug.ok.ini", "log message for debug2.", "#0 /path/to/test.php(0)"],
            ["INFO",  "log.test.debug.ok.ini", "log message for debug3."],
            ["INFO",  "log.test.debug.ok.ini", "log message for debug4.", "#0 /path/to/test.php(0)"],
            ["WARN",  "log.test.debug.ok.ini", "log message for debug5."],
            ["WARN",  "log.test.debug.ok.ini", "log message for debug6.", "#0 /path/to/test.php(0)"],
            ["ERROR", "log.test.debug.ok.ini", "log message for debug7."],
            ["ERROR", "log.test.debug.ok.ini", "log message for debug8.", "#0 /path/to/test.php(0)"],
            ["FATAL", "log.test.debug.ok.ini", "log message for debug9."],
            ["FATAL", "log.test.debug.ok.ini", "log message for debug10.", "#0 /path/to/test.php(0)"]
        ];
    }

    public function logLevelInfoProvider()
    {
        return [
            ["DEBUG", "log.test.info.ok.ini", "log message for info1."],
            ["DEBUG", "log.test.info.ok.ini", "log message for info2.", "#0 /path/to/test.php(0)"],
            ["INFO",  "log.test.info.ok.ini", "log message for info3."],
            ["INFO",  "log.test.info.ok.ini", "log message for info4.", "#0 /path/to/test.php(0)"],
            ["WARN",  "log.test.info.ok.ini", "log message for info5."],
            ["WARN",  "log.test.info.ok.ini", "log message for info6.", "#0 /path/to/test.php(0)"],
            ["ERROR", "log.test.info.ok.ini", "log message for info7."],
            ["ERROR", "log.test.info.ok.ini", "log message for info8.", "#0 /path/to/test.php(0)"],
            ["FATAL", "log.test.info.ok.ini", "log message for info9."],
            ["FATAL", "log.test.info.ok.ini", "log message for info10.", "#0 /path/to/test.php(0)"]
        ];
    }

    public function logLevelWarnProvider()
    {
        return [
            ["DEBUG", "log.test.warn.ok.ini", "log message for warn1."],
            ["DEBUG", "log.test.warn.ok.ini", "log message for warn2.", "#0 /path/to/test.php(0)"],
            ["INFO",  "log.test.warn.ok.ini", "log message for warn3."],
            ["INFO",  "log.test.warn.ok.ini", "log message for warn4.", "#0 /path/to/test.php(0)"],
            ["WARN",  "log.test.warn.ok.ini", "log message for warn5."],
            ["WARN",  "log.test.warn.ok.ini", "log message for warn6.", "#0 /path/to/test.php(0)"],
            ["ERROR", "log.test.warn.ok.ini", "log message for warn7."],
            ["ERROR", "log.test.warn.ok.ini", "log message for warn8.", "#0 /path/to/test.php(0)"],
            ["FATAL", "log.test.warn.ok.ini", "log message for warn9."],
            ["FATAL", "log.test.warn.ok.ini", "log message for warn10.", "#0 /path/to/test.php(0)"]
        ];
    }

    public function logLevelErrorProvider()
    {
        return [
            ["DEBUG", "log.test.error.ok.ini", "log message for error1."],
            ["DEBUG", "log.test.error.ok.ini", "log message for error2.", "#0 /path/to/test.php(0)"],
            ["INFO",  "log.test.error.ok.ini", "log message for error3."],
            ["INFO",  "log.test.error.ok.ini", "log message for error4.", "#0 /path/to/test.php(0)"],
            ["WARN",  "log.test.error.ok.ini", "log message for error5."],
            ["WARN",  "log.test.error.ok.ini", "log message for error6.", "#0 /path/to/test.php(0)"],
            ["ERROR", "log.test.error.ok.ini", "log message for error7."],
            ["ERROR", "log.test.error.ok.ini", "log message for error8.", "#0 /path/to/test.php(0)"],
            ["FATAL", "log.test.error.ok.ini", "log message for error9."],
            ["FATAL", "log.test.error.ok.ini", "log message for error10.", "#0 /path/to/test.php(0)"]
        ];
    }

    public function logLevelFatalProvider()
    {
        return [
            ["DEBUG", "log.test.fatal.ok.ini", "log message for fatal1."],
            ["DEBUG", "log.test.fatal.ok.ini", "log message for fatal2.", "#0 /path/to/test.php(0)"],
            ["INFO",  "log.test.fatal.ok.ini", "log message for fatal3."],
            ["INFO",  "log.test.fatal.ok.ini", "log message for fatal4.", "#0 /path/to/test.php(0)"],
            ["WARN",  "log.test.fatal.ok.ini", "log message for fatal5."],
            ["WARN",  "log.test.fatal.ok.ini", "log message for fatal6.", "#0 /path/to/test.php(0)"],
            ["ERROR", "log.test.fatal.ok.ini", "log message for fatal7."],
            ["ERROR", "log.test.fatal.ok.ini", "log message for fatal8.", "#0 /path/to/test.php(0)"],
            ["FATAL", "log.test.fatal.ok.ini", "log message for fatal9."],
            ["FATAL", "log.test.fatal.ok.ini", "log message for fatal10.", "#0 /path/to/test.php(0)"]
        ];
    }

    public function rotateCycleDayWithinProvider()
    {
        return [
            ["log.test.ok1.rotate.ini", 1],
            ["log.test.ok1.rotate.ini", 23]
        ];
    }

    public function rotateCycleDayProvider()
    {
        return [
            ["log.test.ok1.rotate.ini", 24],
            ["log.test.ok1.rotate.ini", 25]
        ];
    }

    public function rotateCycleWeekWithinProvider()
    {
        return [
            ["log.test.ok2.rotate.ini", 24],
            ["log.test.ok2.rotate.ini", 24 * 7 -1]
        ];
    }

    public function rotateCycleWeekProvider()
    {
        return [
            ["log.test.ok2.rotate.ini", 24 * 7],
            ["log.test.ok2.rotate.ini", 24 * 7 + 1]
        ];
    }

    public function rotateCycleMonthWithinProvider()
    {
        $day_of_month = 24 * intval(date("t", time()));

        return [
            ["log.test.ok3.rotate.ini", 24],
            ["log.test.ok3.rotate.ini", $day_of_month - 1]
        ];
    }

    public function rotateCycleMonthProvider()
    {
        $day_of_month = 24 * intval(date("t", time()));

        return [
            ["log.test.ok3.rotate.ini", $day_of_month],
            ["log.test.ok3.rotate.ini", $day_of_month + 1]
        ];
    }

    public function rotateCycleYearWithinProvider()
    {
        $day_of_year = 24 * 365;
        $year = date("Y");
        if (($year % 4 === 0 && $year % 100 !== 0) || $year % 400 === 0) {
            $day_of_year = 24 * 366;
        }

        return [
            ["log.test.ok4.rotate.ini", 24],
            ["log.test.ok4.rotate.ini", $day_of_year - 1]
        ];
    }

    public function rotateCycleYearProvider()
    {
        $day_of_year = 24 * 365;
        $year = date("Y");
        if (($year % 4 === 0 && $year % 100 !== 0) || $year % 400 === 0) {
            $day_of_year = 24 * 366;
        }

        return array(
            array("log.test.ok4.rotate.ini", $day_of_year),
            array("log.test.ok4.rotate.ini", $day_of_year + 1)
        );
    }

    public function rotateSizeProvider()
    {
        return [
            ["log.test.ok5.rotate.ini", 1024],
            ["log.test.ok5.rotate.ini", 1025]
        ];
    }

    public function rotateSizeWithinProvider()
    {
        return [
            ["log.test.ok5.rotate.ini", 1023],
            ["log.test.ok5.rotate.ini", 0]
        ];
    }

    public function notFoundRotateCycleConfigProvider()
    {
        return [
            ["log.test.ng1.rotate.ini"]
        ];
    }

    public function invalidRotateCycleConfigProvider()
    {
        return [
            ["log.test.ng2.rotate.ini"]
        ];
    }

    public function notFoundRotateSizeConfigProvider()
    {
        return [
            ["log.test.ng3.rotate.ini"]
        ];
    }

    public function invalidRotateSizeConfigProvider()
    {
        return [
            ["log.test.ng4.rotate.ini"]
        ];
    }
}
