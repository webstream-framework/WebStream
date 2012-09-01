<?php
/**
 * DB接続クラス
 * @author Ryuichi TANAKA.
 * @since 2010/08/10
 */
class DatabaseCore {
    /** DBマネージャオブジェクト */
    private $manager = null;
    
    /** DBMS識別子 */
    const MYSQL = "mysql";
    const SQLITE = "sqlite";
    
    /**
     * インスタンス生成を禁止
     */
    private function __construct() {}
    
    /**
     * DBオブジェクトを返却する
     * @param String DBMS名
     * @param String データベース名
     * @param Map 接続オプション
     * @return DBオブジェクト
     */
    protected function getManager($options) {
        $manager = null;
        $dbms = $options["dbms"];
        try {
            // MySQL
            if ($dbms === self::MYSQL) {
                $manager = new PDO(
                    "mysql:host=" . $options["host"] . "; dbname=" . $options["dbname"],
                    $options["user"],
                    $options["password"]
                );
                $manager->query("SET NAMES utf8");
            }
            // Sqlite
            else if ($dbms === self::SQLITE) {
                $manager = new PDO("sqlite:" . Utility::getRoot() . "/db/" . $options["dbfile"]);
            }
        }
        catch(PDOException $e) {
            Logger::error($e->getMessage(), $e->getTraceAsString());
            throw $e;
        }
        return $manager;
    }
}

/**
 * DBアクセスクラス
 * @author Ryuichi TANAKA.
 * @since 2010/08/10
 */
class Database extends DatabaseCore {
    /** DBマネージャ */
    private static $dbaccessor = null;
    /** 設定ファイルパス */
    private static $config_path = "config/database.ini";
    /** SQL */
    private $sql = null;
    /** バインド変数 */
    private $bind = null;
    /** ステートメント変数 */
    private $stmt = null;
    
    /**
     * インスタンス生成を禁止
     */
    private function __construct() {}
        
    /**
     * メンバ変数を初期化
     */
    private function init() {
        $this->sql  = null;
        $this->bind = null;
        $this->stmt = null;
    }
    
    /**
     * DBマネージャを返却する
     * @return Object DBマネージャ
     */
    public static function manager() {
        if (!is_object(self::$dbaccessor)) {
            self::$dbaccessor = new Database();
        }
        self::$dbaccessor->init();
        return self::$dbaccessor;
    }
    
    /**
     * SQLを実行する
     * @param String SQL
     * @param Array bindする変数
     * @return boolean 実行結果
     */
    private function execSQL($sql, $bind = array()) {
        $result = false;
        try {
            $config = Utility::parseConfig(self::$config_path);
            $stmt = parent::getManager($config)->prepare($sql);
            Logger::info("Executed SQL: " . $sql);
            Logger::info("Bind statement: " . implode(",", $bind));
            if ($stmt === false) {
                throw new Exception("Can't create statement. - ". $sql);
            }
            foreach ($bind as $key => $value) {
                if (preg_match("/^[0-9]+$/", $value) && is_int($value)) {
                    $stmt->bindValue($key, $value, PDO::PARAM_INT);
                }
                else {
                    $stmt->bindValue($key, $value, PDO::PARAM_STR);
                }
            }
            $exec = $stmt->execute();
            if ($exec === true) {
                $this->stmt = $stmt;
                $result = true;
            }
            else {
                $messages = $stmt->errorInfo();
                $message = $messages[2];
                $sqlState = "(SQL STATE: ${messages[0]})";
                $errorCode = "(ERROR CODE: ${messages[1]})";
                throw new Exception("${message} ${sqlState} ${errorCode}");
            }
        }
        catch (Exception $e) {
            Logger::error($e->getMessage(), $e->getTraceAsString());
            throw $e;
        }
        return $result;
    }
    
    /**
     * SELECTを除くCRUDを実行する
     * @param String SQL
     * @param Array bindする変数
     * @return boolean 実行結果
     */
    private function execCRUD($sql, $bind) {
        $result = $this->execSQL($sql, $bind);
        $this->init();
        return $result;
    }
    
    /**
     * テーブルのフィールド情報を返却する
     * @param String テーブル名
     * @return Array フィールド情報
     */
    public function columnInfo($table) {
        $sql = "SELECT * FROM ${table}";
        $this->execSQL($sql);
        $i = 0;
        $columns = array();
        while ($column = $this->stmt->getColumnMeta($i++)) {
            $columns[] = $column;
        }
        $this->init();
        return $columns;
    }
    
    /**
     * SELECTを実行する
     * @param String SQL
     * @param Array bindする変数
     * @return Object 実行結果
     */
    public function select($sql, $bind = array()) {
        if ($this->sql  !== $sql  ||
            $this->bind !== $bind ||
            $this->stmt === null) {
            $this->sql  = $sql;
            $this->bind = $bind;
            $this->stmt = null;
            $this->execSQL($sql, $bind);
        }
            
        // 取得結果を連想配列に入れる
        $result = array();
        while ($row = $this->stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }
        
        return $result;  
    }

    /**
     * INSERTを実行する
     * @param String SQL
     * @param Array bindする変数
     * @return boolean 実行結果
     */
    public function insert($sql, $bind) {
        return $this->execCRUD($sql, $bind);
    }

    /**
     * UPDATEを実行する
     * @param String SQL
     * @param Array bindする変数
     * @return boolean 実行結果
     */
    public function update($sql, $bind) {
        return $this->execCRUD($sql, $bind);
    }

    /**
     * DELETEを実行する
     * @param String SQL
     * @param Array bindする変数
     * @return boolean 実行結果
     */
    public function delete($sql, $bind = array()) {
        return $this->execCRUD($sql, $bind);
    }
    
    /**
     * CREATEを実行する
     * @param String SQL
     * @param Array bindする変数
     * @return boolean 実行結果
     */
    public function create($sql, $bind = array()) {
        return $this->execCRUD($sql, $bind);
    }
    
    /**
     * DROPを実行する
     * @param String SQL
     * @param Array bindする変数
     * @return boolean 実行結果
     */
    public function drop($sql, $bind = array()) {
        return $this->execCRUD($sql, $bind);
    }
}
