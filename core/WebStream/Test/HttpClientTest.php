<?php
namespace WebStream\Test;

use WebStream\Module\HttpClient;
use WebStream\Module\Logger;

require_once 'TestBase.php';
require_once 'TestConstant.php';

/**
 * HttpAgentクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/10/01
 * @version 0.4.1
 */
class HttpClientTest extends TestBase
{
    use TestConstant;

    public function setUp()
    {
        parent::setUp();
        Logger::init($this->getLogConfigPath() . "/log.test.debug.ok.ini");
    }

    /**
     * 正常系
     * 外部サイトからGETでコンテンツを取得できること
     * @test
     */
    public function okGet()
    {
        $http = new HttpClient();
        $html = $http->get($this->getHtmlUrl());
        $status_code = $http->getStatusCode();
        $this->assertTrue(!empty($html));
        $this->assertEquals($status_code, 200);
    }

    /**
     * 正常系
     * RSSをGETで取得できること
     * @test
     */
    public function okGetRss()
    {
        $http = new HttpClient();
        $rss = $http->get($this->getRssUrl());
        $status_code = $http->getStatusCode();
        $this->assertTrue(!empty($rss));
        $this->assertEquals($status_code, 200);
    }

    /**
     * 正常系
     * JSONをGETで取得できること
     * @test
     */
    public function okGetJson()
    {
        $http = new HttpClient();
        $json = $http->get($this->getJsonUrl());
        $status_code = $http->getStatusCode();
        $this->assertTrue(!empty($json));
        $this->assertEquals($status_code, 200);
    }

    /**
     * 正常系
     * Basic認証がかかっているURLからコンテンツを取得できること
     * @test
     */
    public function okGetBasicAuth()
    {
        $http = new HttpClient([
            "basic_auth_id" => "guest",
            "basic_auth_password" => "1234"
        ]);
        $html = $http->get($this->getBasicAuthUrl());
        $status_code = $http->getStatusCode();
        $this->assertTrue(!empty($html));
        $this->assertEquals($status_code, 200);
    }

    /**
     * 正常系
     * 外部サイトからPOSTでコンテンツ取得できること
     * @test
     */
    public function okPost()
    {
        $http = new HttpClient();
        $html = $http->post($this->getHtmlUrl(), ["name" => "test"]);
        $status_code = $http->getStatusCode();
        $this->assertTrue(!empty($html));
        $this->assertEquals($status_code, 200);
    }

    /**
     * 異常系
     * GETで外部サイトがタイムアウトになった場合、
     * コンテンツ取得に失敗しステータスコード408が取得できること
     * @test
     */
    public function ngGetTimeout()
    {
        $http = new HttpClient([
            "timeout" => 0.001
        ]);
        $html = $http->get($this->getHtmlUrl());
        $status_code = $http->getStatusCode();
        $this->assertTrue(empty($html));
        $this->assertEquals($status_code, 408);
    }

    /**
     * 異常系
     * POSTで外部サイトがタイムアウトになった場合、
     * コンテンツ取得に失敗しステータスコード408が取得できること
     * @test
     */
    public function ngPostTimeout()
    {
        $http = new HttpClient([
            "timeout" => 0.001
        ]);
        $html = $http->post($this->getHtmlUrl(), ["name" => "test"]);
        $status_code = $http->getStatusCode();
        $this->assertTrue(empty($html));
        $this->assertEquals($status_code, 408);
    }

    /**
     * 異常系
     * GETで外部サイトが存在しない場合、
     * コンテンツ取得に失敗しステータスコード404が取得できること
     * @test
     */
    public function ngGetNotFound()
    {
        $http = new HttpClient();
        $html = $http->get($this->getNotFoundUrl());
        $status_code = $http->getStatusCode();
        $this->assertTrue(empty($html));
        $this->assertEquals($status_code, 404);
    }

    /**
     * 異常系
     * POSTで外部サイトが存在しない場合、
     * コンテンツ取得に失敗しステータスコード404が取得できること
     * @test
     */
    public function ngPostNotFound()
    {
        $http = new HttpClient();
        $html = $http->post($this->getNotFoundUrl(), ["name" => "test"]);
        $status_code = $http->getStatusCode();
        $this->assertTrue(empty($html));
        $this->assertEquals($status_code, 404);
    }

    /**
     * 異常系
     * Basic認証がかかっているURLに対して、
     * 間違ったID・パスワードを指定するとコンテンツ取得に失敗し
     * ステータスコード401が取得できること
     * @test
     */
    public function ngGetBasicAuth()
    {
        $http = new HttpClient([
            "basic_auth_id" => "dummy",
            "basic_auth_password" => "dummy"
        ]);
        $html = $http->get($this->getBasicAuthUrl());
        $status_code = $http->getStatusCode();
        $this->assertTrue(empty($html));
        $this->assertEquals($status_code, 401);
    }
}
