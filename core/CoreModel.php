<?php
namespace WebStream;
/**
 * CoreModelクラス
 * @author Ryuichi TANAKA.
 * @since 2012/09/01
 */
class CoreModel {
    /** DBインスタンス */
    private $db;
    /** テーブル名のリスト */
    private $tables = array();
    /** SQL */
    private $sql;
    /** BIND */
    private $bind = array();
    /** カラム情報 */
    private $columns;
    /** SQLプロパティファイル情報 */
    private $sqlProperties = array();
    /** メソッドアノテーション情報 */
    private $methodAnnotations;
    /** DataMapperインスタンス */
    private $mapper;
    
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
        if (preg_match('/(?:select(?:_by_array)?|(?:dele|upda)te|insert)/', $method)) {
            // Modelクラスからの呼び出し元メソッド名を取得
            $callerMethod = $this->getCallerMethodName();
            // メソッド名から@SQLのインジェクト値を取得する
            $sqlProperties = $this->getInjectedValue($callerMethod);
            // @Propertiesで指定した設定ファイル内のキーに一致するSQLを取得
            $sql = $this->getDefinedSQL($sqlProperties["prefix"], $sqlProperties["key"]);
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
        $sqlKeyAnnotations = $annotation->methods("@SQL");
        $dbname = !empty($databaseAnnotation) ? $databaseAnnotation[0]->value : null;
        $this->setTables($tableAnnotations);
        $this->dbConnection($dbname);
        $this->setColumns();
        $this->sqlProperties($sqlAnnotations);
        $this->sqlKeys($sqlKeyAnnotations);
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
     * @param Object Propertiesプロパティファイルアノテーション
     */
    private function sqlProperties($sqlAnnotations) {
        // Propertiesファイルに記述するキーはファイル内で一意にする必要がある。
        $properties = array();
        foreach ($sqlAnnotations as $annotation) {
            $filepath = $annotation->value;
            $properties = Utility::parseConfig($filepath);
            if ($properties === null) {
                $errorMsg = "Properties file specified by @Properties annotation is not found: ${filepath}";
                throw new ResourceNotFoundException($errorMsg);
            }
            // 拡張子を除いたファイル名をキーとする
            $prefix = basename($filepath, ".properties");
            foreach ($properties as $key => $sql) {
                if (!array_key_exists($prefix, $this->sqlProperties)) {
                    $this->sqlProperties[$prefix] = array();
                }
                if (!array_key_exists($key, $this->sqlProperties[$prefix])) {
                    $this->sqlProperties[$prefix][$key] = $sql;
                }
            }
        }
    }
    
    /**
     * SQLキーの妥当性を検証する
     * @param Object SQLキーアノテーション
     */
    private function sqlKeys($sqlKeyAnnotations) {
        foreach ($sqlKeyAnnotations as $sqlKeyAnnotation) {
            if (!preg_match('/(^[a-zA-Z]{1}[a-zA-Z0-9]+)\.(.*)$/', $sqlKeyAnnotation->value)) {
                $ca = $sqlKeyAnnotation->className . "#" . $sqlKeyAnnotation->methodName;
                $errorMsg = "'$sqlKeyAnnotation->value' is invalid @SQL annotation value at $ca";
                throw new ResourceNotFoundException($errorMsg);
            }
        }
        $this->methodAnnotations = $sqlKeyAnnotations;
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
     * @return Hash インジェクト値
     */
    private function getInjectedValue($method) {
        foreach ($this->methodAnnotations as $annotation) {
            if ($annotation->methodName === $method) {
                // [propertiesファイルprefix].[キー]の形式でなければ例外を出す
                // users.aaa.bbb はOKとする
                if (preg_match('/(^[a-zA-Z]{1}[a-zA-Z0-9]+)\.(.*)$/', $annotation->value, $matches)) {
                    return array(
                        "prefix" => $matches[1],
                        "key" => $matches[2]
                    );
                }
                else {
                    $errorMsg = "Invalid @SQL annotation value: $annotation->value";
                    throw new ResourceNotFoundException($errorMsg);
                }
            }
        }
    }
    
    /**
     * Propertiesファイルに定義されたSQLを返却する
     * @param String Propertiesキー
     * @param String SQLキー
     * @return String SQL
     */
    private function getDefinedSQL($prefix, $key) {
        if (array_key_exists($prefix, $this->sqlProperties) &&
            array_key_exists($key, $this->sqlProperties[$prefix])) {
            return $this->sqlProperties[$prefix][$key];
        }
    }
}
