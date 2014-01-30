<?php
namespace WebStream\Test;

use WebStream\Module\Logger;
use WebStream\Module\HttpClient;
use WebStream\Test\DataProvider\HeaderProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/HeaderProvider.php';

/**
 * @Headerのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/10/19
 * @version 0.4
 */
class HeaderTest extends TestBase
{
    use HeaderProvider, TestConstant;

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
     * contentType属性で指定したContentType
     * のファイルを正常に出力できること
     * @test
     * @dataProvider contentTypeProvider
     */
    public function okContentType($path, $contentType)
    {
        $http = new HttpClient();
        $http->get($this->getDocumentRootURL() . $path);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($http->getContentType(), $contentType);
    }

    /**
     * 正常系
     * allowMethodで指定したメソッドでアクセスできること
     * @test
     * @dataProvider allowMethodProvider
     */
    public function okAllowMethod($path, $params, $method)
    {
        $http = new HttpClient();
        $http->{$method}($this->getDocumentRootURL() . $path, $params);
        $this->assertEquals($http->getStatusCode(), 200);
    }

    /**
     * 正常系
     * allowMethodで複数指定したメソッドでアクセスできること
     * @test
     * @dataProvider allowMethodsProvider
     */
    public function okAllowMethods($path, $params, $method)
    {
        $http = new HttpClient();
        $http->{$method}($this->getDocumentRootURL() . $path, $params);
        $this->assertEquals($http->getStatusCode(), 200);
    }

    /**
     * 正常系
     * contentTypeとallowMethodを同時に指定してアクセスできること
     * @test
     * @dataProvider contentTypeAndAllowMethodProvider
     */
    public function okContentTypeAndAllowMethod($path, $contentType, $params, $method)
    {
        $http = new HttpClient();
        $http->{$method}($this->getDocumentRootURL() . $path, $params);
        $this->assertEquals($http->getContentType(), $contentType);
        $this->assertEquals($http->getStatusCode(), 200);
    }

    /**
     * 異常系
     * allowMethodで指定していないメソッドでアクセスできないこと
     * @test
     * @dataProvider notAllowMethodProvider
     */
    public function ngAllowMethod($path, $params, $method)
    {
        $http = new HttpClient();
        $http->{$method}($this->getDocumentRootURL() . $path, $params);
        $this->assertEquals($http->getStatusCode(), 405);
    }

    /**
     * 異常系
     * allowMethodで複数指定したメソッドのいずれにも含まれないメソッドでアクセスできないこと
     * @test
     * @dataProvider notAllowMethodsProvider
     */
    public function ngAllowMethods($path, $params, $method)
    {
        $http = new HttpClient();
        $http->{$method}($this->getDocumentRootURL() . $path, $params);
        $this->assertEquals($http->getStatusCode(), 405);
    }
}
