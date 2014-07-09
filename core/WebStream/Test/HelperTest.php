<?php
namespace WebStream\Test;

use WebStream\Module\Logger;
use WebStream\Module\HttpClient;
use WebStream\Test\DataProvider\HelperProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/HelperProvider.php';

/**
 * Helperテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/10/19
 * @version 0.4
 */
class HelperTest extends TestBase
{
    use HelperProvider, TestConstant;

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
     * 正常にアクセスできること
     * @test
     * @dataProvider helperProvider
     */
    public function okHelper($path, $response)
    {
        $http = new HttpClient();
        $path = $this->getDocumentRootURL() . $path;
        $responseText = $http->get($path);
        $this->assertEquals($response, $responseText);
        $this->assertEquals($http->getStatusCode(), 200);
    }
}
