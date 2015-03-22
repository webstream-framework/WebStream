<?php
namespace WebStream\Test;

use WebStream\Module\Cache;
use WebStream\Module\Logger;
use WebStream\Module\HttpClient;
use WebStream\Test\DataProvider\TemplateProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/TemplateProvider.php';

/**
 * @Templateのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/10/19
 * @version 0.4
 */
class TemplateTest extends TestBase
{
    use TemplateProvider, TestConstant;

    public function setUp()
    {
        Logger::init($this->getLogConfigPath() . "/log.test.debug.ok.ini");
        parent::setUp();
    }

    public function tearDown()
    {
    }

    /**
     * 正常系
     * Basicテンプレートで正常にアクセスできること
     * @test
     * @dataProvider basicTemplateProvider
     */
    public function okBasicTemplate($path, $response)
    {
        $url = $this->getDocumentRootURL() . $path;
        $responseText = file_get_contents($url);
        $this->assertEquals($response, $responseText);
    }

    /**
     * 正常系
     * テンプレート内で展開されたJavaScriptコードがエスケープされること
     * @test
     * @dataProvider basicTemplateJavaScriptEscapeProvider
     */
    public function okBasicTemplateJavaScriptEscape($path, $response)
    {
        $url = $this->getDocumentRootURL() . $path;
        $responseText = file_get_contents($url);
        $this->assertEquals($response, $responseText);
    }

    /**
     * 正常系
     * テンプレート内で展開されたJavaScriptコードがエスケープされること
     * @test
     * @dataProvider basicTemplateHtmlEscapeProvider
     */
    public function okBasicTemplateHtmlEscape($path, $response)
    {
        $url = $this->getDocumentRootURL() . $path;
        $responseText = file_get_contents($url);
        $this->assertEquals($response, $responseText);
    }

    /**
     * 正常系
     * テンプレート内で展開されたXMLが取得できること
     * @test
     * @dataProvider basicTemplateXmlProvider
     */
    public function okBasicTemplateXmlProvider($path, $response)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $path;
        $http->get($url);
        $header = $http->getResponseHeader();
        $responseContentType = null;
        if (preg_match("/^Content-Type:\s(.+?);/", $header[13], $matches)) {
            $responseContentType = $matches[1];
        }

        $this->assertEquals($response, $responseContentType);
    }

    /**
     * 正常系
     * Basicテンプレートの有効期限が切れたらキャッシュファイルが削除されること
     * @test
     * @dataProvider basicTemplateCacheTimeProvider
     */
    public function okBasicTemplateCacheTimeout($path)
    {
        $url = $this->getDocumentRootURL() . $path;
        $responseText = file_get_contents($url);

        $dir = $this->getProjectRootPath() . $this->getCacheDir();
        $cache = new Cache($dir);
        $data = $cache->get("webstream-cache-test_basic_template-index12");
        $this->assertEquals($data, $responseText);
        sleep(10);
        $data = $cache->get("webstream-cache-test_basic_template-index12");
        $this->assertEquals($data, null);
    }

    /**
     * 正常系
     * Twigテンプレートで正常にアクセスできること
     * @test
     * @dataProvider twigTemplateProvider
     */
    public function okTwigTemplate($path, $response)
    {
        $url = $this->getDocumentRootURL() . $path;
        $responseText = file_get_contents($url);
        $this->assertEquals($response, $responseText);
    }

    /**
     * 異常系
     * テンプレート記述に間違いがある場合、例外が発生すること
     * @test
     * @dataProvider basicTemplateErrorProvider
     */
    public function ngBasicTemplate($path, $statusCode)
    {
        $url = $this->getDocumentRootURL() . $path;
        $response = @file_get_contents($url);
        list($version, $responseStatusCode, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($responseStatusCode, $statusCode);
    }

    /**
     * 異常系
     * テンプレート記述に間違いがある場合、例外が発生すること
     * @test
     * @dataProvider twigTemplateErrorProvider
     */
    public function ngTwigTemplate($path, $statusCode)
    {
        $url = $this->getDocumentRootURL() . $path;
        $response = @file_get_contents($url);
        list($version, $responseStatusCode, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($responseStatusCode, $statusCode);
    }
}
