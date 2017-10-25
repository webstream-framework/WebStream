<?php
namespace WebStream\Annotation\Test\Providers;

use WebStream\Annotation\Test\Fixtures\TemplateFixture1;

/**
 * TemplateAnnotationProvider
 * @author Ryuichi TANAKA.
 * @since 2017/01/14
 * @version 0.7
 */
trait TemplateAnnotationProvider
{
    public function okProvider()
    {
        return [
            [TemplateFixture1::class, "action1", [[
                'filename' => "test.tmpl",
                'engine' => "WebStream\Template\Basic"
            ]]],
            [TemplateFixture1::class, "action2", [[
                'filename' => "test.tmpl",
                'engine' => "WebStream\Template\Basic"
            ]]],
            [TemplateFixture1::class, "action3", [[
                'filename' => "test.tmpl",
                'engine' => "WebStream\Template\Twig",
                'debug' => false
            ]]],
            [TemplateFixture1::class, "action4", [[
                'filename' => "test.tmpl",
                'engine' => "WebStream\Template\Twig",
                'debug' => true
            ]]],
            [TemplateFixture1::class, "action5", [[
                'filename' => "test.tmpl",
                'engine' => "WebStream\Template\Basic",
                'cacheTime' => 10
            ]]]
        ];
    }

    public function ngProvider()
    {
        return [
            [TemplateFixture1::class, "action6"],
            [TemplateFixture1::class, "action7"],
            [TemplateFixture1::class, "action8"],
            [TemplateFixture1::class, "action9"]
        ];
    }
}
