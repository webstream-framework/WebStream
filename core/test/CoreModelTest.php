<?php
namespace WebStream\Test;
use WebStream\Utility;
use WebStream\Database;
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

        } catch (\Exception $e) {}
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
        } catch (\Exception $e) {}
        
        $sql = 'INSERT INTO users (user_id, user_name) VALUES (:user_id, :user_name)';
        $bind = array("user_id" => "YRYR000001", "user_name" => "kyouko");
        $db->insert($sql, $bind);
        $bind = array("user_id" => "YRYR000002", "user_name" => "yui");
        $db->insert($sql, $bind);
    }
    
    /**
     * 正常系
     * 外部からSQLを指定して結果を取得できること
     * @dataProvider executeSQL
     */
    public function testOkExecuteSQL($sql, $bind = array()) {
        $model = new \WebStream\TestModel2();
        $result = $model->select($sql, $bind);
        $this->assertNotCount(0, $result);
    }
    
    /**
     * 正常系
     * カラムマッピングで結果が取得できること
     */
    public function testOkExecuteMapping() {
        $model = new \WebStream\TestModel2();
        $result = $model->userName();
        $this->assertNotCount(0, $result);
        foreach ($result as $elem) {
            $this->assertArrayHasKey("user_name", $elem);
        }
    }
    
    /**
     * 正常系
     * カラムマッピングで複数のテーブルを指定し、カラム名が重複した場合、マージされた結果が取得できること
     */
    public function testOkExecuteMappingWithMerge() {
        $model = new \WebStream\TestModel7();
        $result = $model->userName();
        $this->assertNotCount(0, $result);
        $isOk1 = false;
        $isOk2 = false;
        $isOk3 = false;
        foreach ($result as $elem) {
            if ($elem["user_name"] == "yui") {
                $isOk1 = true;
            }
            if ($elem["user_name"] == "azusa") {
                $isOk2 = true;
            }
            if ($isOk1 && $isOk2) break;
        }
        foreach ($result as $elem) {
            if ($elem["user_name"] == "okarin") {
                $isOk3 = true;
            }
            if ($isOk3) break;
        }
        $this->assertTrue($isOk1 && $isOk2 && $isOk3);
    }
    
    /**
     * 正常系
     * 設定ファイルで指定されたDB名を@Databaseアノテーションで変更できること
     */
    public function testOkChangeDatabase() {
        $model = new \WebStream\TestModel3();
        $result = $model->userName();
        $this->assertEquals("kyouko", $result[0]["user_name"]);
    }
    
    /**
     * 正常系
     * @SQLでインジェクトしたSQLを実行出来ること
     */
    public function testOkInjectedSQL() {
        $model = new \WebStream\TestModel2();
        $result = $model->getUserList();
        $this->assertNotCount(0, $result);
    }
    
    /**
     * 正常系
     * @SQLでインジェクトしたSQLをバインド変数付きで実行出来ること
     */
    public function testOkInjectedSQLWithBind() {
        $model = new \WebStream\TestModel2();
        $result = $model->getUserList2(array("name" => "yui"));
        $this->assertNotCount(0, $result);
        $this->assertEquals("yui", $result[0]["user_name"]);
    }
    
    /**
     * 正常系
     * @SQLでインジェクトしたSQLをバインドしてOUTER JOINを含むSQLを実行出来ること
     */
    public function testOkInjectedSQLWithBindByJoin() {
        $model = new \WebStream\TestModel7();
        $result = $model->outerJoin(array("id" => "KON000001"));
        $this->assertNotCount(0, $result);
    }

    /**
     * 異常系
     * カラムマッピングで存在しないカラムに対応するメソッドが指定された場合、例外が発生すること
     * @expectedException WebStream\MethodNotFoundException
     */
    public function testNgExecuteMapping() {
        $model = new \WebStream\TestModel2();
        $model->dummy();
    }
    
    /**
     * 異常系
     * @Propertiesに存在しないパスが指定された場合、例外が発生すること
     * @expectedException WebStream\ResourceNotFoundException
     */
    public function testNgNotFoundPropertiesAnnotation() {
        $model = new \WebStream\TestModel4();
    }
    
    /**
     * 異常系
     * @Tableに存在しない値が指定された場合、例外が発生すること
     * @expectedException WebStream\DatabaseException
     */
    public function testNgNotFoundTableAnnotation() {
        $model = new \WebStream\TestModel5();
    }
    
    /**
     * 異常系
     * @Databaseに存在しない値が指定された場合、例外が発生すること
     * @expectedException WebStream\DatabaseException
     */
    public function testNgNotFoundDatabaseAnnotation() {
        $model = new \WebStream\TestModel6();
    }
    
    /**
     * 異常系
     * @Propertiesファイルが複数指定され、キーが重複した場合、例外が発生すること
     * @expectedException WebStream\DatabasePropertiesException
     */
    public function testNgDuplicatePropertiesKey() {
        $model = new \WebStream\TestModel8();
    }
}
    