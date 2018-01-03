<?php
namespace WebStream\Annotation\Test\Providers;

use WebStream\Annotation\Test\Fixtures\QueryFixture1;
use WebStream\Annotation\Test\Fixtures\QueryFixture2;
use WebStream\Annotation\Test\Fixtures\QueryFixture3;

/**
 * QueryAnnotationProvider
 * @author Ryuichi TANAKA.
 * @since 2017/01/15
 * @version 0.7
 */
trait QueryAnnotationProvider
{
    public function okProvider()
    {
        return [
            [QueryFixture1::class, "action1", dirname(__FILE__) . "/../Fixtures/", "select1",
                ['sql' => 'SQL', 'method' => 'select', 'entity' => null]
            ],
            [QueryFixture1::class, "action1", dirname(__FILE__) . "/../Fixtures/", "select2",
                ['sql' => 'SQL', 'method' => 'select', 'entity' => 'EntityClass']
            ]
        ];
    }

    public function ng1Provider()
    {
        return [
            [QueryFixture2::class, "action1", dirname(__FILE__) . "/../Fixtures/"]
        ];
    }

    public function ng2Provider()
    {
        return [
            [QueryFixture3::class, "action1", dirname(__FILE__) . "/../Fixtures/"]
        ];
    }
}
