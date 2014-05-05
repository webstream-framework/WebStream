<?php
namespace WebStream\Test;

use WebStream\Module\Logger;
use WebStream\Module\HttpClient;
use WebStream\Test\DataProvider\JsonProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/JsonProvider.php';

/**
 * JSONのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2014/05/04
 * @version 0.4
 */
class JsonTest extends TestBase
{
    use JsonProvider, TestConstant;

    public function setUp()
    {
        parent::setUp();
        Logger::init($this->getLogConfigPath() . "/log.test.debug.ok.ini");
    }

    /**
     * 正常系
     * JSONデータを取得できること
     * @test
     * @dataProvider jsonProvider
     */
    public function okJson($path, $json)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $path;
        $response = $http->get($url);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($http->getContentType(), "Content-Type: application/json; charset=UTF-8");
        $this->assertEquals($response, $json);
    }
}
