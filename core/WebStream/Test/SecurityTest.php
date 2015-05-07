<?php
namespace WebStream\Test;

use WebStream\Module\Security;
use WebStream\Module\HttpClient;
use WebStream\Module\Logger;
use WebStream\Test\DataProvider\SecurityProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/SecurityProvider.php';

/**
 * Securityクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/17
 * @version 0.4
 */
class SecurityTest extends TestBase
{
    use SecurityProvider, TestConstant;

    public function setUp()
    {
        Logger::init($this->getLogConfigPath() . "/log.test.debug.ok.ini");
        parent::setUp();
    }

    /**
     * 正常系
     * URLエンコード済みの文字列に制御文字列がある場合、削除されること
     * @test
     * @dataProvider deleteInvisibleCharacterProvider
     */
    public function okDeleteInvisibleCharacter($withinInvisibleStr, $withoutInvisibleStr)
    {
        $this->assertEquals(Security::safetyIn($withinInvisibleStr), rawurldecode($withoutInvisibleStr));
    }

    /**
     * 正常系
     * XSS対象の文字列を置換できること
     * @test
     * @dataProvider replaceXSSStringsProvider
     */
    public function okReplaceXSSStrings($withinXssHtml, $withoutXssHtml)
    {
        $this->assertEquals(Security::safetyOut($withinXssHtml), $withoutXssHtml);
    }

    /**
     * 正常系
     * CSRF対策トークンをformタグに対して自動的に付与できること
     * @test
     * @dataProvider createCsrfTokenProvider
     */
    public function okCreateCsrfToken($path)
    {
        // CSRFテストページのHTMLを取得
        $html = file_get_contents($this->getDocumentRootURL() . $path);
        // DOMを使ってCSRFトークンを抜く
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);
        $token = null;
        $nodeList = $doc->getElementsByTagName("input");
        for ($i = 0; $i < $nodeList->length; $i++) {
            $node = $nodeList->item($i);
            $token = $node->getAttribute("value");
        }
        $this->assertRegExp('/[a-z0-9]{40}/', $token);
    }

    /**
     * 正常系
     * CSRFトークンとセッション値が一致すること
     * @test
     */
    public function okCsrfTokenMatch()
    {
        $html = file_get_contents($this->getDocumentRootURL() . "/csrf_get");
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);
        $token = null;
        $nodeList = $doc->getElementsByTagName("input");
        for ($i = 0; $i < $nodeList->length; $i++) {
            $node = $nodeList->item($i);
            $token = $node->getAttribute("value");
        }
        // SESSION_ID
        $nodeList = $doc->getElementsByTagName("div");
        $session_id = $nodeList->item(0)->nodeValue;

        $this->assertEquals($token, $session_id);
    }

    /**
     * 正常系
     * CSRFチェックに問題がない場合200になること
     * @test
     */
    public function okCsrfRequest()
    {
        $http = new HttpClient();
        $response = $http->get($this->getDocumentRootURL() . "/csrf_post");
        $headers = $http->getResponseHeader();
        $cookieHeaderList = [];
        if (preg_match("/(WSSESS\=.+?;)/", $headers[4], $matches)) {
            $cookieHeaderList[] .= $matches[1] . " ";
        }
        if (preg_match("/(WSSESS_STARTED\=.+?;)/", $headers[8], $matches)) {
            $cookieHeaderList[] .= $matches[1] . " ";
        }

        $cookieHeader = "Cookie: " . implode(" ", $cookieHeaderList);

        $requestHeaders = [
            $cookieHeader
        ];

        $doc = new \DOMDocument();
        @$doc->loadHTML($response);
        $token = null;
        $nodeList = $doc->getElementsByTagName("input");
        for ($i = 0; $i < $nodeList->length; $i++) {
            $node = $nodeList->item($i);
            $token = $node->getAttribute("value");
        }

        $response = $http->post($this->getDocumentRootURL() . "/csrf_post_view", [
            "__CSRF_TOKEN__" => $token, // invalid
            "name" => "hoge"
        ], $requestHeaders);

        $this->assertEquals($response, "ok");
        $this->assertEquals($http->getStatusCode(), 200);
    }

    /**
     * 異常系
     * CSRFトークンが不正な場合、400エラーになること
     * @test
     */
    public function ngCsrfRequest()
    {
        $http = new HttpClient();
        $response = $http->get($this->getDocumentRootURL() . "/csrf_post");
        $headers = $http->getResponseHeader();
        $cookieHeaderList = [];
        if (preg_match("/(WSSESS\=.+?;)/", $headers[4], $matches)) {
            $cookieHeaderList[] .= $matches[1] . " ";
        }
        if (preg_match("/(WSSESS_STARTED\=.+?;)/", $headers[8], $matches)) {
            $cookieHeaderList[] .= $matches[1] . " ";
        }

        $cookieHeader = "Cookie: " . implode(" ", $cookieHeaderList);

        $requestHeaders = [
            $cookieHeader
        ];

        $doc = new \DOMDocument();
        @$doc->loadHTML($response);
        $token = null;
        $nodeList = $doc->getElementsByTagName("input");
        for ($i = 0; $i < $nodeList->length; $i++) {
            $node = $nodeList->item($i);
            $token = $node->getAttribute("value");
        }

        $response = $http->post($this->getDocumentRootURL() . "/csrf_post_view", [
            "__CSRF_TOKEN__" => "xxxxxxxxxxxxxxxxxxxxxxxx", // invalid
            "name" => "hoge"
        ], $requestHeaders);

        $this->assertEquals($http->getStatusCode(), 400);
    }
}
