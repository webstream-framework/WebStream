<?php
/**
 * CoreModelクラス
 * @author Ryuichi TANAKA.
 * @since 2012/09/01
 */
class CoreModel2 {
    /** DBインスタンスを格納するメンバ変数 */
    protected $db;
    /** テーブル名 */
    protected $table;
    /** SQL */
    protected $sql;
    /** BIND */
    protected $bind = array();
    /** カラム情報 */
    protected $columns;
    /** SQLプロパティファイル情報 */
    protected $sqlProperties;
    /** メソッドアノテーション情報 */
    protected $methodAnnotations;
    
    /**
     * コンストラクタ
     */
    public function __construct() {
        $this->initialize();
    }
    
    /**
     * 存在しないメソッドが呼ばれたときの処理
     * @param String メソッド名
     * @param Array 引数のリスト
     * @return mixed 実行結果
     */
    public function __call($method, $arguments) {
        // 通常のSQL実行
        if (preg_match('/(?:(?:inser|selec)t|(?:dele|upda)te)/', $method)) {
            // Modelクラスからの呼び出し元メソッド名を取得
            $callerMethod = $this->getCallerMethodName();
            // メソッド名から@SQLのインジェクト値を取得する
            $sqlPropertiesKey = $this->getInjectedValue($callerMethod);
            // @Propertiesで指定した設定ファイル内のキーに一致するSQLを取得
            $sql = $this->getDefinedSQL($sqlPropertiesKey);
            $bind = array();
            if (!empty($arguments)) {
                foreach ($arguments as $argument) {
                    if (is_string($argument)) {
                        $sql = $argument;
                    }
                    else if (is_array($argument)) {
                        $bind = $argument;
                    }
                }
            }
            return $this->db->{$method}($sql, $bind);
        }
        else if (preg_match('/(?:create|drop)/', $method)) {
            $sql = $arguments[0];
            return $this->db->{$method}($sql);
        }

        return $this->mapper($method, $arguments);
    }
    
    /**
     * モデルの初期処理
     */
    protected function initialize() {
        $annotation = new Annotation(get_class($this));
        $databaseAnnotation = $annotation->classes("@Database");
        $tableAnnotation = $annotation->classes("@Table");
        $sqlAnnotation = $annotation->classes("@Properties");
        $this->methodAnnotations = $annotation->methods("@SQL");
        $this->dbConnection($databaseAnnotation[0]->value);
        $this->table = $tableAnnotation[0]->value;
        $this->columnInfo($this->table);
        $this->sqlProperties($sqlAnnotation[0]->value);
    }
    
    /**
     * 指定したテーブルのカラム情報を設定する
     * @param String テーブル名
     */
    private function columnInfo($table) {
        $this->columns = $this->db->columnInfo($this->table);
    }
    
    /**
     * SQLプロパティ情報を設定する
     * @param String SQLプロパティファイルパス
     */
    private function sqlProperties($filepath) {
        $config = Utility::parseConfig($filepath);
        if ($config === null) {
            $errorMsg = "Properties file specified by @SQL annotation is not found: ${filepath}";
            throw new ResourceNotFoundException($errorMsg);
        }
        $this->sqlProperties = Utility::parseConfig($filepath);
    }
    
    /**
     * メソッド名とカラムをマッピングして結果を返却する
     * @param String メソッド名
     * @param Array 引数のリスト
     * @return Array selectの実行結果
     */
    protected function mapper($method, $arguments) {
        $snakeMethod = Utility::camel2snake($method);
        $columnName = null;
        if ($this->columns != null) {
            foreach ($this->columns as $column) {
                if ($column["name"] === $snakeMethod) {
                    $columnName = $snakeMethod;
                    break;
                }
            }
        }
        
        if ($columnName === null) {
            $className = get_class($this);
            $errorMsg = "Column '$snakeMethod' is not found in Table '$this->table.' " .
                        "$className#$method mapping failed.";
            throw new MethodNotFoundException($errorMsg);
        }
        
        $sql = sprintf("SELECT %s FROM %s", $columnName, $this->table);
        $bind = array();
        $limitSql = "LIMIT :limit OFFSET :offset";
        if (count($arguments) == 1 && is_int($arguments[0])) {
            $bind["limit"] = $arguments[0];
            $bind["offset"] = 0;
            $sql .= " " . $limitSql;
        }
        else if (count($arguments) == 2 && is_int($arguments[0]) && is_int($arguments[1])) {
            $bind["limit"] = $arguments[1];
            $bind["offset"] = $arguments[0];
            $sql .= " " . $limitSql;
        }

        return $this->db->select($sql, $bind);
    }

    /**
     * DB接続する
     * モデルクラスからは参照不可
     * @param String アノテーション定義されたDB名
     */
    private function dbConnection($dbname) {
        $this->db = Database::manager($dbname);
    }
    
    /**
     * 呼び出し元メソッド名を取得する
     * @return String 呼び出し元メソッド名
     */
    private function getCallerMethodName() {
        $trace = debug_backtrace();
        return $trace[3]['function'];
    }
    
    /**
     * @SQLでインジェクトした値を取得する
     * @param String インジェクト対象メソッド名
     * @return String インジェクト値
     */
    private function getInjectedValue($method) {
        foreach ($this->methodAnnotations as $annotation) {
            if ($annotation->name === $method) {
                return $annotation->value;
            }
        }
    }
    
    /**
     * Propertiesファイルに定義されたSQLを返却する
     * @param String SQLキー
     * @return String SQL
     */
    private function getDefinedSQL($key) {
        if (array_key_exists($key, $this->sqlProperties)) {
            return $this->sqlProperties[$key];
        }
    }
}
