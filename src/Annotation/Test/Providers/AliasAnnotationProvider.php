<?php
namespace WebStream\Annotation\Test\Providers;

use WebStream\Annotation\Test\Fixtures\AliasFixture1;
use WebStream\Annotation\Test\Fixtures\AliasFixture2;

/**
 * TemplateAnnotationProvider
 * @author Ryuichi TANAKA.
 * @since 2017/01/10
 * @version 0.7
 */
trait AliasAnnotationProvider
{
    public function okProvider()
    {
        return [
            [AliasFixture1::class, "aliasMethod1", "originMethod1"]
        ];
    }

    public function ngProvider()
    {
        return [
            [AliasFixture2::class, "aliasMethod1", "originMethod1"]
        ];
    }
}
