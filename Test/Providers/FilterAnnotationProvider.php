<?php
namespace WebStream\Annotation\Test\Providers;

use WebStream\Annotation\Test\Fixtures\FilterFixture1;
use WebStream\Annotation\Test\Fixtures\FilterFixture2;
use WebStream\Annotation\Test\Fixtures\FilterFixture3;
use WebStream\Annotation\Test\Fixtures\FilterFixture4;
use WebStream\Annotation\Test\Fixtures\FilterFixture5;
use WebStream\Annotation\Test\Fixtures\FilterFixture6;
use WebStream\Annotation\Test\Fixtures\FilterFixture7;

/**
 * FilterAnnotationProvider
 * @author Ryuichi TANAKA.
 * @since 2017/01/09
 * @version 0.7
 */
trait FilterAnnotationProvider
{
    public function filterOutputProvider()
    {
        return [
            ["b1b2a1a2", FilterFixture1::class, "action"],
            ["beea", FilterFixture2::class, "beforeExceptEnable"],
            ["bbeda", FilterFixture2::class, "beforeExceptDisable"],
            ["baee", FilterFixture2::class, "afterExceptEnable"],
            ["baeda", FilterFixture2::class, "afterExceptDisable"],
            ["bboe", FilterFixture3::class, "beforeOnlyEnable"],
            ["bod", FilterFixture3::class, "beforeOnlyDisable"],
            ["aoea", FilterFixture3::class, "afterOnlyEnable"],
            ["aod", FilterFixture3::class, "afterOnlyDisable"],
            ["beea", FilterFixture4::class, "beforeExceptEnable"],
            ["bee2a", FilterFixture4::class, "beforeExceptEnable2"],
            ["baee", FilterFixture4::class, "afterExceptEnable"],
            ["baee2", FilterFixture4::class, "afterExceptEnable2"],
            ["bboe", FilterFixture5::class, "beforeOnlyEnable"],
            ["bboe2", FilterFixture5::class, "beforeOnlyEnable2"],
            ["aoea", FilterFixture5::class, "afterOnlyEnable"],
            ["aoe2a", FilterFixture5::class, "afterOnlyEnable2"],
            ["seb2", FilterFixture6::class, "skipEnable"],
            ["mse", FilterFixture6::class, "multipleSkipEnable"],
            ["a", FilterFixture7::class, "action"]
        ];
    }
}
