<?php
namespace WebStream\Annotation\Test\Providers;

use WebStream\Annotation\Test\Fixtures\HeaderFixture1;

/**
 * HeaderAnnotationProvider
 * @author Ryuichi TANAKA.
 * @since 2017/01/09
 * @version 0.7
 */
trait HeaderAnnotationProvider
{
    public function okProvider()
    {
        return [
            [HeaderFixture1::class, "action1", "POST", "html"],
            [HeaderFixture1::class, "action2", "POST", "html"],
            [HeaderFixture1::class, "action3", "POST", "html"],
            [HeaderFixture1::class, "action3", "GET", "html"],
            [HeaderFixture1::class, "action5", "GET", "xml"]
        ];
    }

    public function runtimeErrorProvider()
    {
        return [
            [HeaderFixture1::class, "action1", "GET"],
            [HeaderFixture1::class, "action1", "PUT"],
            [HeaderFixture1::class, "action1", "DELETE"]
        ];
    }

    public function annotationErrorProvider()
    {
        return [
            [HeaderFixture1::class, "action4", "GET"],
            [HeaderFixture1::class, "action6", "GET"],
            [HeaderFixture1::class, "action7", "GET"]
        ];
    }
}
