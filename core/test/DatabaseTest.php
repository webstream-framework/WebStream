<?php
namespace WebStream\Test;
use WebStream\Database;
/**
 * Databaseクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/10
 */
require_once 'UnitTestBase.php';

class DatabaseTest extends UnitTestBase {
    private $db;
    
    public function setUp() {
        parent::setUp();
        // ログ出力ディレクトリ、ログレベルをテスト用に変更
        $class = new \ReflectionClass("WebStream\Database");
        $method = $class->getMethod("manager");
        $this->db = $method->invoke($class, null, $this->config_path_mysql);
        $this->db->create($this->create_sql);
    }
    
    public function tearDown() {
        $this->db->drop($this->drop_sql);
    }
    
    /**
     * 正常系
     * INSERTのテストを実行する
     */
    public function testOkInsert() {
        $result = $this->db->insert("INSERT INTO stream_test (name) values (:name)", array(
            "name" => "insert test"
        ));
        $this->assertTrue($result);
    }
    
    /**
     * 正常系
     * SELECTのテストを実行する
     */
    public function testOkSelect() {
        // INSERT
        $this->db->insert("INSERT INTO stream_test (name) values (:name)", array(
            "name" => "select test"
        ));
        // SELECT
        $result = $this->db->select("SELECT name FROM stream_test");
        $name = null;
        foreach ($result as $elem) { $name = $elem["name"]; break; }
        $this->assertEquals("select test", $name);
    }
    
    /**
     * 正常系
     * SELECTの結果がPDOIteratorクラスのインスタンスになっていること
     */
    public function testOkSelectIterator() {
        // INSERT
        $this->db->insert("INSERT INTO stream_test (name) values (:name)", array(
            "name" => "select test"
        ));
        // SELECT
        $result = $this->db->select("SELECT name FROM stream_test");
        $this->assertTrue($result instanceof \WebStream\PDOIterator);
    }
    
    /**
     * 正常系
     * PDOIterator#toArrayによりSELECTの結果が配列になること
     */
    public function testOkSelectArray() {
        // INSERT
        $this->db->insert("INSERT INTO stream_test (name) values (:name)", array(
            "name" => "select test"
        ));
        // SELECT
        $result = $this->db->select("SELECT name FROM stream_test");
        $this->assertTrue(is_array($result->toArray()));
    }

    /**
     * 正常系
     * DatabaseCrud#select_by_arrayにより結果が配列になること
     */
    public function testOkSelectByArray() {
        // INSERT
        $this->db->insert("INSERT INTO stream_test (name) values (:name)", array(
            "name" => "select test"
        ));
        // SELECT BY ARRAY
        $result = $this->db->select_by_array("SELECT name FROM stream_test");
        $this->assertTrue(is_array($result));
    }
    
    /**
     * 正常系
     * UPDATEのテストを実行する
     */
    public function testOkUpdate() {
        // INSERT
        $this->db->insert("INSERT INTO stream_test (name) values (:name)", array(
            "name" => "insert test"
        ));
        // UPDATE
        $this->db->update("UPDATE stream_test SET name = :name", array(
            "name" => "update test"
        ));
        // SELECT
        $result = $this->db->select("SELECT name FROM stream_test");
        $name = null;
        foreach ($result as $elem) { $name = $elem["name"]; break; }
        $this->assertEquals("update test", $name);
    }
    
    /**
     * 正常系
     * DELETEのテストを実行する
     */
    public function testOnDelete() {
        // INSERT
        $this->db->insert("INSERT INTO stream_test (name) values (:name)", array(
            "name" => "delete test"
        ));
        // DELETE
        $result = $this->db->delete("DELETE FROM stream_test");
        $this->assertTrue($result);
    }
}
