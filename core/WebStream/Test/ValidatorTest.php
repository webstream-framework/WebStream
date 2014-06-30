<?php
namespace WebStream\Test;

// use WebStream\Delegate\Validator;
// use WebStream\DI\ServiceLocator;
use WebStream\Module\Logger;
use WebStream\Module\HttpClient;
use WebStream\Test\DataProvider\ValidatorProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/ValidatorProvider.php';

/**
 * Validatorテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/10/19
 * @version 0.4
 */
class ValidatorTest extends TestBase
{
    use ValidatorProvider, TestConstant;

    private $container;
    private $classLoader;

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
     * GETでバリデーションチェックが通ること
     * @test
     * @dataProvider validatorGetProvider
     */
    public function okGetValidator($path, $params)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $path;
        $html = $http->get($url, $params);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($html, current($params));
    }

    /**
     * 正常系
     * POSTでバリデーションチェックが通ること
     * @test
     * @dataProvider validatorPostProvider
     */
    public function okPostValidator($path, $params)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $path;
        $html = $http->post($url, $params);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($html, current($params));
    }

    /**
     * 正常系
     * PUTでバリデーションチェックが通ること
     * @test
     * @dataProvider validatorPutProvider
     */
    public function okPutValidator($path, $params)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $path;
        $html = $http->put($url, $params);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($html, current($params));
    }

    /**
     * 異常系
     * GETでバリデーションエラーが発生したとき、422が返ること
     * @test
     * @dataProvider validatorGetErrorProvider
     */
    public function ngGetValidator($path, $params)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $path;
        $html = $http->get($url, $params);
        $this->assertEquals($http->getStatusCode(), 422);
    }

    /**
     * 異常系
     * POSTでバリデーションエラーが発生したとき、422が返ること
     * @test
     * @dataProvider validatorPostErrorProvider
     */
    public function ngPostValidator($path, $params)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $path;
        $html = $http->post($url, $params);
        $this->assertEquals($http->getStatusCode(), 422);
    }
}
