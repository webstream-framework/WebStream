<?php
namespace WebStream;
/**
 * DBコアクラス
 * @author Ryuichi TANAKA.
 * @since 2010/08/10
 */
class DatabaseCore {
    /** DBマネージャオブジェクト */
    protected $manager = null;
    /** DBコネクション */
    protected $connect;
    /** ステートメント変数 */
    protected $stmt = null;
    /** 設定ファイルパス */
    private static $configPath = "config/database.ini";

    /** DBMS識別子 */
    const MYSQL = "mysql";
    const SQLITE = "sqlite";
    
    /**
     * インスタンス生成を禁止
     */
    private function __construct() {}

    /**
     * メンバ変数を初期化
     * @param String DB名
     * @param String 設定ファイルパス
     */
    protected function init($dbname = null, $configPath = null) {
        $this->stmt = null;
        $this->connect($dbname, $configPath);
    }
    
    /**
     * DBに接続する
     * @param String DB名
     * @param String 設定ファイルパス
     */
    private function connect($dbname = null, $configPath = null) {
        // DBへの接続は設定ファイルまたは@Databaseアノテーションで行う
        // 両方設定されていた場合は@Databaseアノテーションを優先する
        $config = Utility::parseConfig($configPath ?: self::$configPath);
        if ($dbname !== null) {
            $config["dbname"] = $dbname;
            $this->connect = self::getManager($config);
        }
        if ($this->connect === null) {
            $this->connect = self::getManager($config);
        }
    }

    /**
     * SQLを実行する
     * @param String SQL
     * @param Array bindする変数
     * @return boolean 実行結果
     */
    protected function execSQL($sql, $bind = array()) {
        $result = false;
        $this->stmt = null;
        try {
            $stmt = $this->connect->prepare($sql);
            Logger::info("Executed SQL: " . $sql);
            Logger::info("Bind statement: " . implode(",", $bind));
            if ($stmt === false) {
                throw new DatabaseException("Can't create statement. - ". $sql);
            }
            foreach ($bind as $key => $value) {
                if (preg_match("/^[0-9]+$/", $value) && is_int($value)) {
                    $stmt->bindValue($key, $value, \PDO::PARAM_INT);
                }
                else {
                    $stmt->bindValue($key, $value, \PDO::PARAM_STR);
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
        catch (\Exception $e) {
            Logger::error($e->getMessage(), $e->getTraceAsString());
            throw new DatabaseException($e->getMessage());
        }
        return $result;
    }
    
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
                $manager = new \PDO(
                    "mysql:host=" . $options["host"] . "; dbname=" . $options["dbname"],
                    $options["user"],
                    $options["password"],
                    array(
                        \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                        \PDO::ATTR_PERSISTENT => true,
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
                    )
                );
            }
            // Sqlite
            else if ($dbms === self::SQLITE) {
                $manager = new PDO("sqlite:" . Utility::getRoot() . "/db/" . $options["dbfile"]);
            }
        }
        catch(\PDOException $e) {
            Logger::error($e->getMessage(), $e->getTraceAsString());
            throw new DatabaseException($e->getMessage());
        }
        return $manager;
    }
}

/**
 * DBクラッドクラス
 * @author Ryuichi TANAKA.
 * @since 2012/11/24
 */
class DatabaseCrud extends DatabaseCore {
    /**
     * インスタンス生成を禁止
     */
    private function __construct() {}

    /**
     * SELECTを実行する
     * @param String SQL
     * @param Array bindする変数
     * @return Object 実行結果
     */
    public function select($sql, $bind = array()) {
        $this->execSQL($sql, $bind);
        return new PDOIterator($this->stmt);
    }

    /**
     * SELECTを実行し配列で返却する
     * @param String SQL
     * @param Array bindする変数
     * @return Array 実行結果
     */
    public function select_by_array($sql, $bind = array()) {
        return $this->select($sql, $bind)->toArray();
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

}

/**
 * DBアクセスクラス
 * @author Ryuichi TANAKA.
 * @since 2010/08/10
 */
class Database extends DatabaseCrud {
    /** DBマネージャ */
    private static $dbaccessor = null;
    
    /**
     * インスタンス生成を禁止
     */
    private function __construct() {}
        
    /**
     * DBマネージャを返却する
     * @param String DB名
     * @return Object DBマネージャ
     */
    public static function manager($dbname = null, $config = null) {
        if (!is_object(self::$dbaccessor)) {
            self::$dbaccessor = new Database();
        }
        self::$dbaccessor->init($dbname, $config);
        return self::$dbaccessor;
    }
    
    /**
     * テーブルのフィールド情報を返却する
     * @param Array テーブルリスト
     * @return Array フィールド情報
     */
    public function columnInfo($tables) {
        $columns = array();
        foreach ($tables as $table) {
            $sql = "SELECT * FROM ${table} LIMIT 1 OFFSET 0";
            $i = 0;
            $columns[$table] = array();
            try {
                $this->execSQL($sql);
                while ($column = $this->stmt->getColumnMeta($i++)) {
                    $columns[$table][] = $column["name"];
                }
                $this->init();
            }
            catch (\Exception $e) {
                Logger::error($e->getMessage(), $e->getTraceAsString());
                throw new DatabaseException($e->getMessage());
            }
        }
        return $columns;
    }
}

/**
 * PDO用イテレータクラス
 * @author Ryuichi TANAKA.
 * @since 2012/10/02
 */
class PDOIterator implements \Iterator {
    /** ステートメントオブジェクト */
    private $stmt;
    /** 列データ */
    private $row;
    /** インデックス位置 */
    private $position;
    
    /**
     * コンストラクタ
     * @param Object ステートメントオブジェクト
     */
    public function __construct($stmt) {
        $this->stmt = $stmt;
        $this->position = 0;
    }
    
    /**
     * デストラクタ
     */
    public function __destruct() {
        $this->stmt = null;
    }
    
    /**
     * 検索結果を全て配列として返却する
     */
    public function toArray() {
        return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * イテレータを巻き戻す
     */
    public function rewind() {
        $this->row = $this->stmt->fetch(\PDO::FETCH_ASSOC);
        $this->position = 0;
    }
    
    /**
     * 現在の要素を返却する
     * @return Hash 列データ
     */
    public function current() {
        return $this->row;
    }
    
    /**
     * 現在の要素のキーを返却する
     * @return Integer キー
     */
    public function key() {
        return $this->position;
    }

    /**
     * 次の要素に進む
     */
    public function next() {
        $this->row = $this->stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * 現在位置が有効かどうかを調べる
     * @return Boolean 有効かどうか
     */
    public function valid() {
        return $this->row !== false;
    }
}
