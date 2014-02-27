<?php
namespace WebStream\Test\TestData\Sample\App\Service;

use WebStream\Core\CoreService;

class TestTemplateService extends CoreService
{
    public function getName1()
    {
        return "hoge";
    }

    public function getName2()
    {
        return "printemps";
    }

    public function getXmlString()
    {
        return <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<items>
    <item>
        <bar>\x09\x0a\x0d</bar>
    </item>
</items>
XML;
    }
}
