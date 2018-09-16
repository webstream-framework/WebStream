<?php
namespace WebStream\Annotation\Test\Providers;

use WebStream\Annotation\Test\Fixtures\DatabaseFixture1;
use WebStream\Annotation\Test\Fixtures\DatabaseFixture2;
use WebStream\Annotation\Test\Fixtures\DatabaseDriverFixture;

/**
 * DatabaseAnnotationProvider
 * @author Ryuichi TANAKA.
 * @since 2017/01/14
 * @version 0.7
 */
trait DatabaseAnnotationProvider
{
    public function okProvider()
    {
        return [
            [DatabaseFixture1::class, "action1", dirname(__FILE__) . "/../Fixtures/", [[
                'filepath' => realpath(dirname(__FILE__) . "/../Fixtures/DatabaseFixture1.php"),
                'configPath' => realpath(dirname(__FILE__) . "/../Fixtures/database.config.ini"),
                'driverClassPath' => DatabaseDriverFixture::class
            ]]]
        ];
    }

    public function ngProvider()
    {
        return [
            [DatabaseFixture2::class, "action1"]
        ];
    }
}
