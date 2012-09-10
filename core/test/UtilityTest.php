<?php
namespace WebStream\Test;
use WebStream\Utility;
use WebStream\HttpAgent;
/**
 * Utilityクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2012/01/15
 */
require_once 'UnitTestBase.php';

class UtilityTest extends UnitTestBase {
    
    public function setUp() {
        parent::setUp();
    }
    
    /**
     * 正常系
     * 文字列をキャメルケースからスネークケースに置換できること
     * @dataProvider camel2SnakeProvider
     */
    public function testCamel2Snake($to, $from) {
        $this->assertEquals(Utility::camel2snake($from), $to);
    }
    
    /**
     * 正常系
     * XMLオブジェクトを配列に変換できること
     * @dataProvider xml2ArrayProvider
     */
    public function testXml2Array($url) {
        $http = new HttpAgent();
        $xml = simplexml_load_string($http->get($url));
        $array = Utility::xml2array($xml);
        $this->assertTrue(is_array($array));
        $this->assertTrue(is_array($array["@attributes"]));
    }
    
    /**
     * 正常系
     * 文字列をエンコードし、正常にデコードできること
     * @dataProvider encodeAndDecodeProvider
     */
    public function testEncodeAndDecode($data) {
        $enc_data = Utility::encode($data);
        $dec_data = Utility::decode($enc_data);
        $this->assertEquals($data, $dec_data);
    }
    
    /**
     * 正常系
     * 文字列をすケークケースからアッパーキャメルケースに置換できること
     * @dataProvider snake2UpperCamelProvider
     */
    public function testSnake2UpperCamel($to, $from) {
        $this->assertEquals(Utility::snake2ucamel($from), $to);
    }

    /**
     * 正常系
     * 文字列をすケークケースからローワーキャメルケースに置換できること
     * @dataProvider snake2LowerCamelProvider
     */
    public function testSnake2LowerCamel($to, $from) {
        $this->assertEquals(Utility::snake2lcamel($from), $to);
    }
}
