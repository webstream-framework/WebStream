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
        $this->setUpTable1();
        $this->setUpTable2();
    }
    
    public function setUpTable1() {
        $db = Database::manager("test");
        try {
            $createTable1 = <<< SQL
CREATE TABLE users (
    id int(0) not null auto_increment,
    user_id varchar(32) not null,
    user_name varchar(128) not null,
    primary key (id)
);
SQL;
            $db->create($createTable1);
            
            $createTable2 = <<< SQL
CREATE TABLE users2 (
    id int(0) not null auto_increment,
    user_id varchar(32) not null,
    user_name varchar(128) not null,
    primary key (id)
);
SQL;
            $db->create($createTable2);

        } catch (Exception $e) {}
        $sql = 'INSERT INTO users (user_id, user_name) VALUES (:user_id, :user_name)';
        $bind = array("user_id" => "KON000001", "user_name" => "yui");
        $db->insert($sql, $bind);
        $bind = array("user_id" => "KON000002", "user_name" => "azusa");
        $db->insert($sql, $bind);
        
        $sql = 'INSERT INTO users2 (user_id, user_name) VALUES (:user_id, :user_name)';
        $bind = array("user_id" => "KON000001", "user_name" => "okarin");
        $db->insert($sql, $bind);
    }
    
    public function setUpTable2() {
        $db = Database::manager("test2");
        try {
            $createTable2 = <<< SQL
CREATE TABLE users (
    id int(0) not null auto_increment,
    user_id varchar(32) not null,
    user_name varchar(128) not null,
    primary key (id)
);
SQL;
            $db->create($createTable2);
        } catch (Exception $e) {}
        
        $sql = 'INSERT INTO users (user_id, user_name) VALUES (:user_id, :user_name)';
        $bind = array("user_id" => "YRYR000001", "user_name" => "kyouko");
        $db->insert($sql, $bind);
        $bind = array("user_id" => "YRYR000002", "user_name" => "yui");
        $db->insert($sql, $bind);
    }
    
    public function testOkttt() {
        $model = new TestModel7();
        $mapper = $model->getMapper("users", "users2");
        var_dump($mapper->userName());
    }
    
    
    // /**
     // * 正常系
     // * 外部からSQLを指定して結果を取得できること
     // * @dataProvider executeSQL
     // */
    // public function testOkExecuteSQL($sql, $bind = array()) {
        // $model = new TestModel2();
        // $result = $model->select($sql, $bind);
        // $this->assertNotCount(0, $result);
    // }
//     
    // /**
     // * 正常系
     // * カラムマッピングで結果が取得できること
     // */
    // public function testOkExecuteMapping() {
        // $model = new TestModel2();
        // $result = $model->userName();
        // $this->assertNotCount(0, $result);
        // foreach ($result as $elem) {
            // $this->assertArrayHasKey("user_name", $elem);
        // }
    // }
//     
    // /**
     // * 正常系
     // * 設定ファイルで指定されたDB名を@Databaseアノテーションで変更できること
     // */
    // public function testOkChangeDatabase() {
        // $model = new TestModel3();
        // $result = $model->userName();
        // $this->assertEquals("kyouko", $result[0]["user_name"]);
    // }
//     
    // /**
     // * 正常系
     // * @SQLでインジェクトしたSQLを実行出来ること
     // */
    // public function testOkInjectedSQL() {
        // $model = new TestModel2();
        // $result = $model->getUserList();
        // $this->assertNotCount(0, $result);
    // }
//     
    // /**
     // * 正常系
     // * @SQLでインジェクトしたSQLをバインド変数付きで実行出来ること
     // */
    // public function testOkInjectedSQLWithBind() {
        // $model = new TestModel2();
        // $result = $model->getUserList2(array("id" => 1));
        // $this->assertCount(1, $result);
        // $this->assertEquals("1", $result[0]["id"]);
    // }
//     
    // public function testTtt() {
        // $model = new TestModel7();
        // $result = $model->join();
//         
        // // TODO JOIN対応に向けて、まずは@Tableで複数のテーブルをInjectできるように
        // // しないとだめ。
//         
//         
        // var_dump($result);
    // }
//     
//     
    // /**
     // * 異常系
     // * カラムマッピングで存在しないカラムに対応するメソッドが指定された場合、例外が発生すること
     // * @expectedException MethodNotFoundException
     // */
    // public function testNgExecuteMapping() {
        // $model = new TestModel2();
        // $model->dummy();
    // }
//     
    // /**
     // * 異常系
     // * @Propertiesに存在しないパスが指定された場合、例外が発生すること
     // * @expectedException ResourceNotFoundException
     // */
    // public function testNgNotFoundPropertiesAnnotation() {
        // $model = new TestModel4();
    // }
//     
    // /**
     // * 異常系
     // * @Tableに存在しない値が指定された場合、例外が発生すること
     // * @expectedException DatabaseException
     // */
    // public function testNgNotFoundTableAnnotation() {
        // $model = new TestModel5();
    // }
//     
    // /**
     // * 異常系
     // * @Databaseに存在しない値が指定された場合、例外が発生すること
     // * @expectedException DatabaseException
     // */
    // public function testNgNotFoundDatabaseAnnotation() {
        // $model = new TestModel6();
    // }
}
    