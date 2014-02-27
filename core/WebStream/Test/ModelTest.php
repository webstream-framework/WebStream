<?php
namespace WebStream\Test;

use WebStream\Module\Logger;
use WebStream\Module\HttpClient;

require_once 'TestBase.php';
require_once 'TestConstant.php';

/**
 * Modelクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2013/12/07
 * @version 0.4
 */
class ModelTest extends TestBase
{
    use TestConstant;

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
     * ViewからModelオブジェクトを認識できること
     * @test
     */
    public function okModelFromView()
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . "/test_template/model/is_model";
        $html = $http->get($url);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($html, "WebStream\Test\TestData\Sample\App\Model\TestTemplateWithModelModel");
    }

    /**
     * 正常系
     * ViewからModelを経由してDBアクセスできること
     * @test
     */
    public function okAccessDBFromView()
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . "/test_template/model/access_db";
        $html = $http->get($url);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($html, "nicomaki");
    }
}
