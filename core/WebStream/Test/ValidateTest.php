<?php
namespace WebStream\Test;

use WebStream\Module\Logger;
use WebStream\Module\HttpClient;
use WebStream\Test\DataProvider\ValidateProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/ValidateProvider.php';

/**
 * Validateテストクラス
 * @author Ryuichi TANAKA.
 * @since 2015/04/01
 * @version 0.4
 */
class ValidateTest extends TestBase
{
    use ValidateProvider, TestConstant;

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
     * バリデーションチェックを通過すること
     * @test
     * @dataProvider validProvider
     */
    public function okValid($path, $method, $key, $value)
    {
        $http = new HttpClient();
        if ($method === "all") {
            $methods = ["get", "post", "put"];
            foreach ($methods as $method) {
                $url = $this->getDocumentRootURL() . $path;
                $response = call_user_func_array([$http, $method], [$url, [$key => $value]]);
                $this->assertEquals($http->getStatusCode(), 200);
                $this->assertEquals($response, $value);
            }
        } else {
            $url = $this->getDocumentRootURL() . $path;
            $response = call_user_func_array([$http, $method], [$url, [$key => $value]]);
            $this->assertEquals($http->getStatusCode(), 200);
            $this->assertEquals($response, $value);
        }
    }

    /**
     * 異常系
     * バリデーションチェックを通過しないこと
     * @test
     * @dataProvider validateErrorProvider
     */
    public function ngInvalid($path, $method, $key = null, $value = null)
    {
        $http = new HttpClient();
        if ($method === "all") {
            $methods = ["get", "post", "put"];
            foreach ($methods as $method) {
                $url = $this->getDocumentRootURL() . $path;
                $response = null;
                if ($key === null && $value === null) {
                    $response = call_user_func_array([$http, $method], [$url, []]);
                } else {
                    $response = call_user_func_array([$http, $method], [$url, [$key => $value]]);
                }
                $this->assertEquals($response, "WebStream\Exception\Extend\ValidateException");
            }
        } else {
            $url = $this->getDocumentRootURL() . $path;
            $response = null;
            if ($key === null && $value === null) {
                $response = call_user_func_array([$http, $method], [$url, []]);
            } else {
                $response = call_user_func_array([$http, $method], [$url, [$key => $value]]);
            }
            $this->assertEquals($http->getStatusCode(), 200);
            $this->assertEquals($response, "WebStream\Exception\Extend\ValidateException");
        }
    }

    /**
     * 異常系
     * バリデーションルールが間違っている場合、例外が発生すること
     * @test
     * @dataProvider invalidRuleProvider
     */
    public function ngRule($path, $response)
    {
        $url = $this->getDocumentRootURL() . $path;
        $http = new HttpClient();
        $this->assertEquals($response, $http->get($url));
    }

    /**
     * 異常系
     * Validateアノテーションが間違っている場合、例外が発生すること
     * @test
     * @dataProvider invalidAnnotationProvider
     */
    public function ngInvalidAnnotation($path, $response)
    {
        $url = $this->getDocumentRootURL() . $path;
        $http = new HttpClient();
        $this->assertEquals($response, $http->get($url));
    }
}
