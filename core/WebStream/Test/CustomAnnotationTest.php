<?php
namespace WebStream\Test;

use WebStream\Module\HttpClient;
use WebStream\Module\Logger;
use WebStream\Test\DataProvider\CustomAnnotationProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/CustomAnnotationProvider.php';

/**
 * CustomAnnotaionのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2015/02/20
 * @version 0.4
 */
class CustomAnnotationTest extends TestBase
{
    use CustomAnnotationProvider, TestConstant;

    public function setUp()
    {
        Logger::init($this->getLogConfigPath() . "/log.test.debug.ok.ini");
        parent::setUp();
    }

    /**
     * 正常系
     * クラスに対するカスタムアノテーションが実行できること
     * @test
     * @dataProvider classProvider
     */
    public function okClassCustomAnnotation($path, $response)
    {
        $http = new HttpClient();
        $result = $http->get($this->getDocumentRootURL() . $path);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($response, $result);
    }

    /**
     * 正常系
     * メソッドに対するカスタムアノテーションが実行できること
     * @test
     * @dataProvider methodProvider
     */
    public function okMethodCustomAnnotation($path, $response)
    {
        $http = new HttpClient();
        $result = $http->get($this->getDocumentRootURL() . $path);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($response, $result);
    }

    /**
     * 正常系
     * プロパティに対するカスタムアノテーションが実行できること
     * @test
     * @dataProvider propertyProvider
     */
    public function okPropertyCustomAnnotation($path, $response)
    {
        $http = new HttpClient();
        $result = $http->get($this->getDocumentRootURL() . $path);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($response, $result);
    }
}
