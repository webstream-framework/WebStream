<?php
/**
 * CoreModelクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2012/09/01
 */
require_once 'UnitTestBase.php';

class CoreModelTest extends UnitTestBase {
    public function setUp() {
        parent::setUp();
        require_once Utility::getRoot() . "/core/test/testdata/app/models/TestModel2.php";
    }
    
    /**
     * 正常系
     * 外部からSQLを指定して結果を取得できること
     * @dataProvider executeSQL
     */
    public function testOkExecuteSQL($sql, $bind = array()) {
        $model = new TestModel2();
        $result = $model->select($sql, $bind);
        $this->assertNotCount(0, $result);
    }
    
    /**
     * 正常系
     * カラムマッピングで結果が取得できること
     */
    public function testOkExecuteMapping() {
        $model = new TestModel2();
        $result = $model->userName();
        $this->assertCount(2, $result);
        foreach ($result as $elem) {
            $this->assertArrayHasKey("user_name", $elem);
        }
    }
    
    /**
     * 異常系
     * カラムマッピングで存在しないカラムに対応するメソッドが指定された場合、例外が発生すること
     * @expectedException MethodNotFoundException
     */
    public function testngExecuteMapping() {
        $model = new TestModel2();
        $model->dummy();
    }
}
    