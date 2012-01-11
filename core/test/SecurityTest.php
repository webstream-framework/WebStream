<?php
/**
 * Securityクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/17
 */
require_once 'UnitTestBase.php';

class SecurityTest extends UnitTestBase {
    private $security;
    
    public function setUp() {
        parent::setUp();
        $this->security = new Security();
    }
    
    /**
     * 正常系
     * URLエンコード済みの文字列に制御文字列がある場合、削除されること
     * @dataProvider deleteInvisibleCharacterProvider
     */
    public function testOkDeleteInvisibleCharacter($within_invisible_str, $without_invisible_str) {
        $this->assertEquals($this->security->safetyIn($within_invisible_str), 
                            rawurldecode($without_invisible_str));
    }
    
    /**
     * 正常系
     * XSS対象の文字列を置換できること
     * @dataProvider replaceXSSStringsProvider
     */
    public function testOkReplaceXSSStrings($within_xss_html, $without_xss_html) {
        $this->assertEquals($this->security->safetyOut($within_xss_html), $without_xss_html);
    }
    
    /**
     * 正常系
     * CSRF対策トークンをformタグに対して自動的に付与できること
     */
    public function testOkCreateCsrfToken() {
        // CSRFテストページのHTMLを取得
        $html = file_get_contents($this->root_url . "/csrf");
        // DOMを使ってCSRFトークンを抜く
        $doc = new DOMDocument();
        @$doc->loadHTML($html);
        $token = null;
        $nodeList = $doc->getElementsByTagName("input");
        for ($i = 0; $i < $nodeList->length; $i++) {
            $node = $nodeList->item($i);
            $token = $node->getAttribute("value");
        }
        $this->assertRegExp('/[a-z0-9]{40}/', $token);
    }
}
