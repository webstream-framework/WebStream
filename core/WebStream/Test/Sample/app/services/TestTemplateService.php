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

    public function showCode1()
    {
        return "honoka\nkotori";
    }

    public function showCode2()
    {
        return "honoka\rumi";
    }

    public function showCode3()
    {
        return "kotori\r\numi";
    }

    public function showCode4()
    {
        return "nico\u2028\u2029\u0085maki";
    }

    public function showCode5()
    {
        return "'erichika'";
    }

    public function showCode6()
    {
        return '"nozomi"';
    }

    public function showCode7()
    {
        return "<script></script>";
    }

    public function showCode8()
    {
        return "rin\v\fpana";
    }

    public function showHtml1()
    {
        return "<p>test</p>";
    }

    public function showPhp1()
    {
        return "<p>test</p>";
    }
}
