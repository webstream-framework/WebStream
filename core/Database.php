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
            throw new DatabaseException($e);
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
    /** DBコネクション */
    private $connect;
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
     * @param String DB名
     */
    private function init($dbname = null) {
        $this->sql  = null;
        $this->bind = null;
        $this->stmt = null;
        $this->connect($dbname);
    }
    
    /**
     * DBマネージャを返却する
     * @param String DB名
     * @return Object DBマネージャ
     */
    public static function manager($dbname = null) {
        if (!is_object(self::$dbaccessor)) {
            self::$dbaccessor = new Database();
        }
        self::$dbaccessor->init($dbname);
        return self::$dbaccessor;
    }
    
    /**
     * DBに接続する
     * @param String DB名
     */
    private function connect($dbname = null) {
        // DBへの接続は設定ファイルまたは@Databaseアノテーションで行う
        // 両方設定されていた場合は@Databaseアノテーションを優先する
        $config = Utility::parseConfig(self::$config_path);
        if ($dbname !== null) {
            $config["dbname"] = $dbname;
            $this->connect = parent::getManager($config);
        }
        if ($this->connect === null) {
            $this->connect = parent::getManager($config);
        }
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
            $stmt = $this->connect->prepare($sql);
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
                throw new DatabaseException("${message} ${sqlState} ${errorCode}");
            }
        }
        catch (Exception $e) {
            Logger::error($e->getMessage(), $e->getTraceAsString());
            throw new DatabaseException($e);
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
        try {
            $this->execSQL($sql);
            $i = 0;
            $columns = array();
            while ($column = $this->stmt->getColumnMeta($i++)) {
                $columns[] = $column;
            }
            $this->init();
            return $columns;
        }
        catch (Exception $e) {
            Logger::error($e->getMessage(), $e->getTraceAsString());
            throw new DatabaseException($e);
        }
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
