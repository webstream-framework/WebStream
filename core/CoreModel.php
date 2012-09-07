<?php
/**
 * CoreModelクラス
 * @author Ryuichi TANAKA.
 * @since 2012/09/01
 */
class CoreModel {
    /** DBインスタンス */
    protected $db;
    /** テーブル名のリスト */
    protected $tables = array();
    /** SQL */
    protected $sql;
    /** BIND */
    protected $bind = array();
    /** カラム情報 */
    protected $columns;
    /** SQLプロパティファイル情報 */
    protected $sqlProperties = array();
    /** メソッドアノテーション情報 */
    protected $methodAnnotations;
    /** DataMapperインスタンス */
    protected $mapper;
    
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
     * @return Array 実行結果
     */
    public function __call($method, $arguments) {
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
        
        return $this->mapper->{$method}($arguments);
    }
    
    /**
     * モデルの初期処理
     */
    protected function initialize() {
        $annotation = new Annotation(get_class($this));
        $databaseAnnotation = $annotation->classes("@Database");
        $tableAnnotations = $annotation->classes("@Table");
        $sqlAnnotations = $annotation->classes("@Properties");
        $this->methodAnnotations = $annotation->methods("@SQL");
        $dbname = !empty($databaseAnnotation) ? $databaseAnnotation[0]->value : null;
        $this->setTables($tableAnnotations);
        $this->dbConnection($dbname);
        $this->setColumns();
        $this->sqlProperties($sqlAnnotations);
        $this->setMapper();
    }
    
    /**
     * DataMapperインスタンスを返却する
     */
    public function setMapper() {
        $this->mapper = DataMapper::get($this->db, $this->columns, $this->tables);
    }
    
    /**
     * テーブル一覧を設定する
     * @param Object テーブルアノテーションオブジェクト
     */
    private function setTables($tableAnnotations) {
        foreach ($tableAnnotations as $annotation) {
            $this->tables[] = $annotation->value;
        }
    }
    
    /**
     * 指定したテーブルのカラム情報を設定する
     */
    private function setColumns() {
        $this->columns = $this->db->columnInfo($this->tables);
    }
    
    /**
     * SQLプロパティ情報を設定する
     * @param Object SQLプロパティファイルアノテーション
     */
    private function sqlProperties($sqlAnnotations) {
        // Propertiesファイルに記述するキーは全体で一意にする必要がある。
        // 重複のキーが合った場合は例外とする。
        $properties = array();
        foreach ($sqlAnnotations as $annotation) {
            $filepath = $annotation->value;
            $config = Utility::parseConfig($filepath);
            if ($config === null) {
                $errorMsg = "Properties file specified by @SQL annotation is not found: ${filepath}";
                throw new ResourceNotFoundException($errorMsg);
            }
            $properties = Utility::parseConfig($filepath);
            foreach ($properties as $key => $sql) {
                if (array_key_exists($key, $this->sqlProperties)) {
                    $errorMsg = "Properties key is duplicated: ${key}";
                    throw new DatabasePropertiesException($errorMsg);
                }
                $this->sqlProperties[$key] = $sql;
            }
        }
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
            if ($annotation->methodName === $method) {
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
