<?php
namespace WebStream\Test\TestData\Sample\App\Helper;

use WebStream\Core\CoreHelper;

class TestBasicTemplateHelper extends CoreHelper
{
    public function help1()
    {
        echo "index7";
    }

    public function help2()
    {
        echo "index9";
    }

    public function xml()
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

    public function javascript1()
    {
        return "honoka\nkotori";
    }

    public function javascript2()
    {
        return "honoka\rumi";
    }

    public function javascript3()
    {
        return "kotori\r\numi";
    }

    public function javascript4()
    {
        return "nico\u2028\u2029\u0085maki";
    }

    public function javascript5()
    {
        return "'erichika'";
    }

    public function javascript6()
    {
        return '"nozomi"';
    }

    public function javascript7()
    {
        return "<script></script>";
    }

    public function javascript8()
    {
        return "rin\v\fpana";
    }

    public function html()
    {
        return "<p>test</p>";
    }
}
