<?php
namespace WebStream\Test;

use WebStream\Module\Logger;
use WebStream\Module\HttpClient;
use WebStream\Test\DataProvider\ServiceProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/ServiceProvider.php';

/**
 * Serviceクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2013/12/07
 * @version 0.4
 */
class ServiceTest extends TestBase
{
    use ServiceProvider, TestConstant;

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
     * Serviceを正常に呼び出せること
     * @test
     * @dataProvider serviceProvider
     */
    public function okService($path, $response)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $path;
        $html = $http->get($url);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($html, $response);
    }

    /**
     * 正常系
     * Serviceクラスが存在しない場合、直接Modelクラスを呼び出せること
     * @test
     * @dataProvider noServiceClass
     */
    public function okNoServiceClass($path, $str)
    {
        $url = $this->getDocumentRootURL() . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, $str);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "200");
    }

    /**
     * 正常系
     * Serviceクラスに該当するメソッドがない場合、
     * Modelクラスのメソッドに移譲できること
     * @test
     * @dataProvider noServiceMethod
     */
    public function okNoServiceMethod($path, $str)
    {
        $url = $this->getDocumentRootURL() . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, $str);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "200");
    }

    /**
     * 正常系
     * Serviceクラスに該当するメソッドがなく、Modelクラスのメソッドに移譲する場合、
     * 引数を正常に渡すことができること
     * @test
     * @dataProvider sendParamFromControllerToModelProvider
     */
    public function okSendParamFromControllerToModel($path, $str)
    {
        $url = $this->getDocumentRootURL() . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, $str);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "200");
    }

    /**
     * 正常系
     * プロパティを明示的に定義しなくてもViewから参照できること
     * ただしプロパティを明示的に定義した場合かつGetterを準備しない場合は参照できないこと
     * @test
     * @dataProvider okPropertyProxyProvider
     */
    public function okPropertyProxy($path, $str)
    {
        $url = $this->getDocumentRootURL() . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, $str);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "200");
    }
}
