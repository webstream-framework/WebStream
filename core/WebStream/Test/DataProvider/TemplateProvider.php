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
    public function basicTemplateProvider()
    {
        return [
            ["/test_template/basic/index1", "index1"],
            ["/test_template/basic/index2", "index2"],
            ["/test_template/basic/index3", "index3"],
            ["/test_template/basic/index4", "index4"],
            ["/test_template/basic/index5", "index5"],
            ["/test_template/basic/index6", "index6"],
            ["/test_template/basic/index7", "index7"],
            ["/test_template/basic/index8", "index8"],
            ["/test_template/basic/index9", "index9"],
            ["/test_template/basic/index10", "index10"],
            ["/test_template/basic/index11", "index11"],
            ["/test_template/basic/index12", "WebStream\Delegate\CoreExceptionDelegator"],
            ["/test_template/basic/index13", "WebStream\Delegate\CoreExceptionDelegator"],
            ["/test_template/basic/index14", "WebStream\Test\TestData\Sample\App\Helper\TestBasicTemplateWithHelperHelper"],
            ["/test_template/basic/index15", "WebStream\Test\TestData\Sample\App\Service\TestBasicTemplateWithServiceService"],
            ["/test_template/basic/index16", "WebStream\Test\TestData\Sample\App\Model\TestBasicTemplateWithModelModel"],
        ];
    }

    public function basicTemplateJavaScriptEscapeProvider()
    {
        return [
            ["/test_template/basic/javascript1", "<html><head><script type='text/javascript'>alert('honoka\u000D\u000Akotori')</script></head></html>"],
            ["/test_template/basic/javascript2", "<html><head><script type='text/javascript'>alert('honoka\u000D\u000Aumi')</script></head></html>"],
            ["/test_template/basic/javascript3", "<html><head><script type='text/javascript'>alert('kotori\u000D\u000Aumi')</script></head></html>"],
            ["/test_template/basic/javascript4", "<html><head><script type='text/javascript'>alert('nico\u000D\u000A\u000D\u000A\u000D\u000Amaki')</script></head></html>"],
            ["/test_template/basic/javascript5", "<html><head><script type='text/javascript'>alert('\u0022erichika\u0022')</script></head></html>"],
            ["/test_template/basic/javascript6", "<html><head><script type='text/javascript'>alert('\u0027nozomi\u0027')</script></head></html>"],
            ["/test_template/basic/javascript7", "<html><head><script type='text/javascript'>alert('\\x3cscript\\x3e\\x3c\/script\\x3e')</script></head></html>"],
            ["/test_template/basic/javascript8", "<html><head><script type='text/javascript'>alert('rin\u000B\u000Cpana')</script></head></html>"]
        ];
    }

    public function basicTemplateXmlProvider()
    {
        return [
            ["/test_template/basic/xml", "application/xml"]
        ];
    }

    public function basicTemplateHtmlEscapeProvider()
    {
        return [
            ["/test_template/basic/html1", "<p>test</p>"],
            ["/test_template/basic/html2", "&lt;p&gt;test&lt;/p&gt;"]
        ];
    }

    public function basicTemplateCacheTimeProvider()
    {
        return [
            ["/test_template/basic/index17"]
        ];
    }

    public function basicTemplateErrorProvider()
    {
        return [
            ["/test_template/basic/error1", 500],
            ["/test_template/basic/error2", 404],
            ["/test_template/basic/error3", 500],
            ["/test_template/basic/error4", 500],
            ["/test_template/basic/error5", 500],
            ["/test_template/basic/error6", 500],
            ["/test_template/basic/error7", 500],
            ["/test_template/basic/error8", 500],
            ["/test_template/basic/error9", 500],
            ["/test_template/basic/error10", 500]
        ];
    }

    public function twigTemplateProvider()
    {
        return [
            ["/test_template/twig/index1", "index1"],
            ["/test_template/twig/index2", "index2"],
            ["/test_template/twig/index3", "honoka"],
            ["/test_template/twig/index4", "index4"],
            ["/test_template/twig/index5", "index5"],
            ["/test_template/twig/index6", "index6"]
        ];
    }

    public function twigTemplateErrorProvider()
    {
        return [
            ["/test_template/twig/error1", 404]
        ];
    }
}
