<?php


class CoreModel2 {
    /** DBインスタンスを格納するメンバ変数 */
    protected $db;
    /** テーブル名 */
    protected $table;
    /** SQL */
    protected $sql;
    /** BIND */
    protected $bind = array();
    /** カラム単位でデータを取得する場合の基本SQL */
    //protected $baseSql;
    /** カラム単位でデータを取得する場合の基本BIND */
    //protected $baseBind = array();
    /** カラム情報 */
    protected $columns;
    
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
            //return $this->execCRUD($method, $arguments[0], $arguments[1]);
            return $this->db->{$method}($arguments[0], $arguments[1]);
        }
        return $this->mapper($method, $arguments);
    }
    
    /**
     * モデルの初期処理
     */
    protected function initialize() {
        $this->dbConnection();
        $annotation = new Annotation(get_class($this));
        $databaseAnnotation = $annotation->classes("@Database");
        $tableAnnotation = $annotation->classes("@Table");
        $sqlAnnotation = $annotation->classes("@SQL");
        $this->table = $tableAnnotation[0]->value;
        //$this->baseSql = "SELECT :column FROM :table";
        //$this->limitSql = "LIMIT :limit OFFSET :offset";
        //$this->baseBind["table"] = $this->table;
        $this->columns = $this->db->columnInfo($this->table);
    }
    
    // /**
     // * SQLを設定する
     // * @param String SQL
     // */
    // final public function sql($sql) {
        // $this->sql = $sql;
    // }
//     
    // /**
     // * PreparedStatementバインドを設定する
     // * @param Array reparedStatementバインド
     // */
    // final public function bind($bind) {
        // $this->bind = $bind;
    // }
    
    // /**
     // * SQLを実行する
     // * @param String 実行するメソッド
     // * @return mixed 実行結果
     // */
    // protected function execCRUD($method, $sql, $bind) {
        // return $this->db->{$method}($sql, $bind);
    // }
    
    /**
     * メソッド名とカラムをマッピングして結果を返却する
     * @param String メソッド名
     * @param Array 引数のリスト
     * @return Array selectの実行結果
     */
    protected function mapper($method, $arguments) {
        $snakeMethod = Utility::camel2snake($method);
        $columnName = null;
        foreach ($this->columns as $column) {
            if ($column["name"] === $snakeMethod) {
                $columnName = $snakeMethod;
                break;
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
     */
    private function dbConnection() {
        $this->db = Database::manager();
    }
}
