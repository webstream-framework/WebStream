<?php
namespace WebStream\Test\DataProvider;

/**
 * TemplateProvider
 * @author Ryuichi TANAKA.
 * @since 2013/10/19
 * @version 0.4
 */
trait TemplateProvider
{
    public function templateProvider()
    {
        return [
            ["/test_template/index1", "index1"],
            ["/test_template/index2", "hoge"],
            ["/test_template/index3", "honoka"],
            ["/test_template/index4", "kotori"],
            ["/test_template/index5", "nicomaki"],
            ["/test_template/index6", "printemps"],
            ["/test_template/index7", "rinchan"]
        ];
    }

    public function templateModelProvider()
    {
        return [
            ["/test_template/model/is_model", "WebStream\Test\TestData\Sample\App\Model\TestTemplateWithModelModel"],
            ["/test_template/model/access_db", "nicomaki"]
        ];
    }

    public function templateHelperProvider()
    {
        return [
            ["/test_template/helper/is_helper", "WebStream\Test\TestData\Sample\App\Helper\TestTemplateWithHelperHelper"],
            ["/test_template/helper/access_helper", "kayochin"]
        ];
    }

    public function templateHtmlEscapeProvider()
    {
        return [
            ["/test_template/html1", "&lt;p&gt;test&lt;/p&gt;"]
        ];
    }

    public function templatePhpEscapeProvider()
    {
        return [
            ["/test_template/php1", "<p>test</p>"]
        ];
    }

    public function templateJavaScriptEscapeProvider()
    {
        return [
            ["/test_template/javascript1", "<html><head><script type='text/javascript'>alert('honoka\u000D\u000Akotori')</script></head></html>"],
            ["/test_template/javascript2", "<html><head><script type='text/javascript'>alert('honoka\u000D\u000Aumi')</script></head></html>"],
            ["/test_template/javascript3", "<html><head><script type='text/javascript'>alert('kotori\u000D\u000Aumi')</script></head></html>"],
            ["/test_template/javascript4", "<html><head><script type='text/javascript'>alert('nico\u000D\u000A\u000D\u000A\u000D\u000Amaki')</script></head></html>"],
            ["/test_template/javascript5", "<html><head><script type='text/javascript'>alert('\u0022erichika\u0022')</script></head></html>"],
            ["/test_template/javascript6", "<html><head><script type='text/javascript'>alert('\u0027nozomi\u0027')</script></head></html>"],
            ["/test_template/javascript7", "<html><head><script type='text/javascript'>alert('\\x3cscript\\x3e\\x3c\/script\\x3e')</script></head></html>"],
            ["/test_template/javascript8", "<html><head><script type='text/javascript'>alert('rin\u000B\u000Cpana')</script></head></html>"]
        ];
    }

    public function templatePhpNonEscapeProvider()
    {
        return [
            ["/test_template/php1", "<p>test</p>"]
        ];
    }

    public function templateErrorProvider()
    {
        return [
            ["/test_template/error1", 404],
            ["/test_template/error2", 404],
            ["/test_template/error3", 500],
            ["/test_template/error4", 500],
            ["/test_template/error5", 500],
            ["/test_template/error6", 500],
            ["/test_template/error7", 500]
        ];
    }

    public function notfoundModelOrHelperProvider()
    {
        return [
            ["/test_template/model/null_model", 500],
            ["/test_template/helper/null_helper", 500]
        ];
    }
}
