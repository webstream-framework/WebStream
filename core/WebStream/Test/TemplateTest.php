<?php
namespace WebStream\Test;

use WebStream\Module\Logger;
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
        parent::setUp();
        Logger::init($this->getLogConfigPath() . "/log.test.debug.ok.ini");
    }

    public function tearDown()
    {
    }

    /**
     * 正常系
     * 正常にアクセスできること
     * @test
     * @dataProvider templateProvider
     */
    public function okTemplate($path, $response)
    {
        $url = $this->getDocumentRootURL() . $path;
        $responseText = file_get_contents($url);
        $this->assertEquals($response, $responseText);
    }

    /**
     * 正常系
     * テンプレート内で正常にModelが処理できること
     * @test
     * @dataProvider templateModelProvider
     */
    public function okTemplateModel($path, $response)
    {
        $url = $this->getDocumentRootURL() . $path;
        $responseText = file_get_contents($url);
        $this->assertEquals($response, $responseText);
    }

    /**
     * 正常系
     * テンプレート内で正常にHelperが処理できること
     * @test
     * @dataProvider templateHelperProvider
     */
    public function okTemplateHelper($path, $response)
    {
        $url = $this->getDocumentRootURL() . $path;
        $responseText = file_get_contents($url);
        $this->assertEquals($response, $responseText);
    }

    /**
     * 正常系
     * テンプレート内で展開されたJavaScriptコードがエスケープされること
     * @test
     * @dataProvider templateJavaScriptEscape
     */
    public function okTemplateJavaScriptEscape($path, $response)
    {
        $url = $this->getDocumentRootURL() . $path;
        $responseText = file_get_contents($url);
        $this->assertEquals($response, $responseText);
    }

    /**
     * 異常系
     * テンプレート記述に間違いがある場合、例外が発生すること
     * @test
     * @dataProvider templateErrorProvider
     */
    public function ngTemplate($path, $statusCode)
    {
        $url = $this->getDocumentRootURL() . $path;
        $response = @file_get_contents($url);
        list($version, $responseStatusCode, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($responseStatusCode, $statusCode);
    }

    /**
     * 異常系
     * ModelオブジェクトまたはHelperオブジェクトが存在しない状態でアクセスした場合、例外が発生すること
     * @test
     * @dataProvider notfoundModelOrHelperProvider
     */
    public function ngNotfoundModelOrHelper($path, $statusCode)
    {
        $url = $this->getDocumentRootURL() . $path;
        $response = @file_get_contents($url);
        list($version, $responseStatusCode, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($responseStatusCode, $statusCode);
    }
}
