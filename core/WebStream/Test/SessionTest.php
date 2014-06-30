<?php
namespace WebStream\Test;

use WebStream\Module\Logger;
use WebStream\Module\HttpClient;

require_once 'TestBase.php';
require_once 'TestConstant.php';

/**
 * SessionTest
 * @author Ryuichi TANAKA.
 * @since 2013/11/27
 * @version 0.4
 */
class SessionTest extends TestBase
{
    use TestConstant;

    public function setUp()
    {
        parent::setUp();
        Logger::init($this->getLogConfigPath() . "/log.test.debug.ok.ini");
    }

    /**
     * 正常系
     * セッションが有効になっている場合、200が取得できること
     * @test
     */
    public function okEnableSession()
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . "/session_no_limit";
        $http->get($url);
        $this->assertEquals($http->getStatusCode(), 200);
        $responseHeader = $http->getResponseHeader();
        $cookie = [];
        foreach ($responseHeader as $header) {
            if (preg_match('/Set-Cookie: (WSSESS=.*?);/', $header, $matches)) {
                $cookie[0] = $matches[1];
            }
            if (preg_match('/Set-Cookie: (WSSESS_STARTED=.*?);/', $header, $matches)) {
                $cookie[1] = $matches[1];
            }
        }
        $cookie = "Cookie: " . $cookie[0] . "; " . $cookie[1];
        $url = $this->getDocumentRootURL() . "/session_index";
        $http->get($url, null, [$cookie]);
        $this->assertEquals($http->getStatusCode(), 200);
    }

    /**
     * 異常系
     * セッションタイムアウトした場合、404が取得できること
     * @test
     */
    public function ngSessionTimeout()
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . "/session_limit";
        $http->get($url);
        $this->assertEquals($http->getStatusCode(), 200);
        sleep(3);
        $responseHeader = $http->getResponseHeader();
        $cookie = [];
        foreach ($responseHeader as $header) {
            if (preg_match('/Set-Cookie: (WSSESS=.*?);/', $header, $matches)) {
                $cookie[0] = $matches[1];
            }
            if (preg_match('/Set-Cookie: (WSSESS_STARTED=.*?);/', $header, $matches)) {
                $cookie[1] = $matches[1];
            }
        }
        $cookie = "Cookie: " . $cookie[0] . "; " . $cookie[1];
        $url = $this->getDocumentRootURL() . "/session_index";
        $http->get($url, null, [$cookie]);
        $this->assertEquals($http->getStatusCode(), 404);
    }
}
