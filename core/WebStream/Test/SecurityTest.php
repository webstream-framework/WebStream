<?php
namespace WebStream\Test;

use WebStream\Module\Security;
use WebStream\Test\DataProvider\SecurityProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/SecurityProvider.php';

/**
 * Securityクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/17
 */
class SecurityTest extends TestBase
{
    use SecurityProvider, TestConstant;

    public function setUp()
    {
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
     * @test
     * CSRF対策トークンをformタグに対して自動的に付与できること
     */
    public function testOkCreateCsrfToken()
    {
        // CSRFテストページのHTMLを取得
        $html = file_get_contents($this->getDocumentRootURL() . "/csrf");
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
}
