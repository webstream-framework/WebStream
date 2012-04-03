<?php
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
     * 文字列をエンコードし、正常にデコードできること
     * @dataProvider encodeAndDecodeProvider
     */
    public function testEncodeAndDecode($data) {
        $enc_data = Utility::encode($data);
        $dec_data = Utility::decode($enc_data);
        $this->assertEquals($data, $dec_data);
    }
}
