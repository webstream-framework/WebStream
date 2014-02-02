<?php
namespace WebStream\Test;

use WebStream\Module\HttpClient;
use WebStream\Module\Logger;
use WebStream\Test\DataProvider\FilterProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/FilterProvider.php';

/**
 * Filterクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/20
 * @version 0.4
 */
class FilterTest extends TestBase
{
    use FilterProvider, TestConstant;

    public function setUp()
    {
        parent::setUp();
        Logger::init($this->getLogConfigPath() . "/log.test.debug.ok.ini");
    }

    /**
     * 正常系
     * before/afterフィルタが実行されること
     * @test
     * @dataProvider filterProvider
     */
    public function okFilter($path, $response)
    {
        $http = new HttpClient();
        $result = $http->get($this->getDocumentRootURL() . $path);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($response, $result);
    }

    /**
     * 正常系
     * before/afterフィルタでexcept/onlyが有効になること
     * @test
     * @dataProvider filterExceptOnlyProvider
     */
    public function okFilterExceptOnly($path, $response)
    {
        $http = new HttpClient();
        $result = $http->get($this->getDocumentRootURL() . $path);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($response, $result);
    }

    /**
     * 異常系
     * initializeフィルタを定義した場合、例外発生すること
     * @test
     */
    public function ngInitializeFilter()
    {
        $http = new HttpClient();
        $http->get($this->getDocumentRootURL() . "/initialize_filter_error");
        $this->assertEquals($http->getStatusCode(), 500);
    }

    /**
     * 異常系
     * initializeフィルタは使用できないこと
     * @test
     */
    public function ngInvalidFilter()
    {
        $http = new HttpClient();
        $http->get($this->getDocumentRootURL() . "/invalid_filter_error");
        $this->assertEquals($http->getStatusCode(), 500);
    }
}
