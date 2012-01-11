<?php
/**
 * HttpAgentクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/10/01
 */
require_once 'UnitTestBase.php';
 
class HttpAgentTest extends UnitTestBase {
    const URL_OK = "http://www.yahoo.co.jp";
    const URL_RSS = "http://headlines.yahoo.co.jp/rss/itmedia_ait.xml";
    const URL_JSON = "http://tepco-usage-api.appspot.com/latest.json";
    const URL_BASIC_AUTH = "http://x68000.q-e-d.net/~68user/net/sample/http-auth/secret.html";
    const URL_NG_NOTFOUND = "http://wwww222.google.co.jp";
    
    /**
     * 正常系
     * 外部サイトからGETでコンテンツを取得できること
     */
    public function testOkGet() {
        $http = new HttpAgent();
        $html = $http->get(self::URL_OK);
        $status_code = $http->getStatusCode();
        $this->assertTrue(!empty($html));
        $this->assertEquals($status_code, 200);
    }
    
    /**
     * 正常系
     * RSSをGETで取得できること
     */
    public function testOkGetRss() {
        $http = new HttpAgent();
        $rss = $http->get(self::URL_RSS);
        $status_code = $http->getStatusCode();
        
        $this->assertTrue(!empty($rss));
        $this->assertEquals($status_code, 200);
    }
    
    /**
     * 正常系
     * JSONをGETで取得できること
     */
    public function testOkGetJson() {
        $http = new HttpAgent();
        $json = $http->get(self::URL_JSON);
        $status_code = $http->getStatusCode();
        
        $this->assertTrue(!empty($json));
        $this->assertEquals($status_code, 200);
    }
    
    /**
     * 正常系
     * Basic認証がかかっているURLからコンテンツを取得できること
     */
    public function testOkGetBasicAuth() {
        $http = new HttpAgent(array(
            "basic_auth_id" => "hoge",
            "basic_auth_password" => "fuga"
        ));
        $html = $http->get(self::URL_BASIC_AUTH);
        $status_code = $http->getStatusCode();
        
        $this->assertTrue(!empty($html));
        $this->assertEquals($status_code, 200);
    }
    
    /**
     * 正常系
     * 外部サイトからPOSTでコンテンツ取得できること
     */
    public function testOkPost() {
        $http = new HttpAgent();
        $html = $http->post(self::URL_OK, array("name" => "test"));
        $status_code = $http->getStatusCode();
        $this->assertTrue(!empty($html));
        $this->assertEquals($status_code, 200);
    }
    
    /**
     * 異常系
     * GETで外部サイトがタイムアウトになった場合、コンテンツ取得に失敗しステータスコード408が取得できること
     */
    public function testNgGetTimeout() {
        $http = new HttpAgent(array(
            "timeout" => 0.001
        ));
        $html = $http->get(self::URL_OK);
        $status_code = $http->getStatusCode();
        
        $this->assertTrue(empty($html));
        $this->assertEquals($status_code, 408);
    }
    
    /**
     * 異常系
     * POSTで外部サイトがタイムアウトになった場合、コンテンツ取得に失敗しステータスコード408が取得できること
     */
    public function testNgPostTimeout() {
        $http = new HttpAgent(array(
            "timeout" => 0.001
        ));
        $html = $http->post(self::URL_OK, array("name" => "test"));
        $status_code = $http->getStatusCode();
        
        $this->assertTrue(empty($html));
        $this->assertEquals($status_code, 408);
    }
    
    /**
     * 異常系
     * GETで外部サイトが存在しない場合、コンテンツ取得に失敗しステータスコード404が取得できること
     */
    public function testNgGetNotFound() {
        $http = new HttpAgent();
        $html = $http->get(self::URL_NG_NOTFOUND);
        $status_code = $http->getStatusCode();
        
        $this->assertTrue(empty($html));
        $this->assertEquals($status_code, 404);
    }
    
    /**
     * 異常系
     * POSTで外部サイトが存在しない場合、コンテンツ取得に失敗しステータスコード404が取得できること
     */
    public function testNgPostNotFound() {
        $http = new HttpAgent();
        $html = $http->post(self::URL_NG_NOTFOUND, array("name" => "test"));
        $status_code = $http->getStatusCode();
        
        $this->assertTrue(empty($html));
        $this->assertEquals($status_code, 404);
    }
    
    /**
     * 異常系
     * Basic認証がかかっているURLに対して、間違ったID・パスワードを指定するとコンテンツ取得に失敗し
     * ステータスコード401が取得できること
     */
    public function testNgGetBasicAuth() {
        $http = new HttpAgent(array(
            "basic_auth_id" => "dummy",
            "basic_auth_password" => "dummy"
        ));
        $html = $http->get(self::URL_BASIC_AUTH);
        $status_code = $http->getStatusCode();
        
        $this->assertTrue(empty($html));
        $this->assertEquals($status_code, 401);
    }
    
}
