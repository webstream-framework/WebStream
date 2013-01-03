<?php
namespace WebStream;
/**
 * Injectionクラス
 * @author Ryuichi TANAKA.
 * @since 2012/11/17
 */
class Injection extends Annotation {
    /** 使用するアノテーション定義 */
    const RENDER     = "@Render";
    const LAYOUT     = "@Layout";
    const REQUEST    = "@Request";
    const BASIC_AUTH = "@BasicAuth";
    const CACHE      = "@Cache";
    const SECURITY   = "@Security";
    const FILTER     = "@Filter";
    const FORMAT     = "@Format";
    const CALLBACK   = "@Callback";
    const ERROR      = "@Error";
    const DATABASE   = "@Database";
    const TABLE      = "@Table";
    const PROPERTIES = "@Properties";
    const SQL        = "@SQL";

    /** Annotationクラスインスタンス */
    private $annotation;

    /**
     * コンストラクタ
     * @param Object ルーティングオブジェクト
     * @param Object コントローラクラスオブジェクト
     */
    public function __construct(\ReflectionClass $controllerClass) {
        parent::__construct($controllerClass);
    }

    /**
     * アノテーション値を返却する
     * アノテーションマークが複数ある場合、初めに定義した1件のみ取得したい場合に使用する
     * @param String アノテーションマーク
     * @param String 対象メソッド名
     * @return String or Array アノテーション値またはリスト
     */
    private function getAnnotationValue($mark, $action) {
        $methodAnnotations = $this->methods($mark);
        foreach ($methodAnnotations as $methodAnnotation) {
            if ($methodAnnotation->methodName === $action) {
                $values[] = $methodAnnotation->value;
            }
        }
        // アノテーションがない場合
        if (empty($values)) {
            return;
        }
        // アノテーション値が1つの場合は始めの要素を返却
        if (count($values) === 1) {
            return $values[0];
        }

        return $values;
    }

    /**
     * @Render, @Layoutアノテーション情報を返却する
     * @param String 対象メソッド名
     * @return Hash レンダリング情報
     */
    public function render($action) {
        $annotations = $this->methods(self::RENDER, self::LAYOUT);
        $method = null;
        $templates = array();
        $methods = array();
        $argList = array();

        $methodName = function($mark) {
            return $mark === Injection::LAYOUT ? '__layout' : '__render';
        };
        foreach ($annotations as $annotation) {
            if ($annotation->methodName === $action) {
                // 一番初めに定義されたレンダリングアノテーションに合わせて実行するメソッドを決定
                if (!isset($method)) {
                    $method = $methodName($annotation->name);
                }
                if ($annotation->index === 0) {
                    $methods[$annotation->value] = $methodName($annotation->name);
                    if (!empty($argList)) $templates[] = $argList;
                    $argList = array();
                }
                $argList[] = $annotation->value;
            }
        }
        if (!empty($argList)) $templates[] = $argList;

        return array(
            "method" => $method,
            "methods" => $methods,
            "templates" => $templates
        );
    }

    /**
     * @Requestアノテーション情報を返却する
     * @param String 抽出対象のメソッド名
     * @return Array 許可されたリクエストメソッドリスト    
     */
    public function request($method) {
        return $this->getAnnotationValue(self::REQUEST, $method);
    }

    /**
     * @BasicAuthアノテーション情報を返却する
     * @param String 抽出対象のメソッド名
     * @return String 基本認証設定ファイルパス
     */
    public function basicAuth($method) {
        return $this->getAnnotationValue(self::BASIC_AUTH, $method);
    }

    /**
     * @Cacheアノテーション情報を返却する
     * @param String 抽出対象のメソッド名
     * @return int キャッシュ有効時間
     */
    public function cache($method) {
        return $this->getAnnotationValue(self::CACHE, $method);
    }

    /**
     * @Securityアノテーション情報を返却する
     * @param String 抽出対象のメソッド名
     * @return String セキュリティアノテーション値 
     */
    public function security($method) {
        return $this->getAnnotationValue(self::SECURITY, $method);
    }

    /**
     * @Filterアノテーション情報を返却する
     * @return Object フィルタアノテーションオブジェクト
     */
    public function filter() {
        return $this->methods(self::FILTER);
    }

    /**
     * @Formatアノテーション情報を返却する
     * @param String 抽出対象のメソッド名
     * @return String 出力形式名
     */
    public function format($method) {
        return $this->getAnnotationValue(self::FORMAT, $method) ?: 'html';
    }

    /**
     * @Callbackアノテーション情報を返却する
     * @param String 抽出対象のメソッド名
     * @return String JSONPコールバック名
     */
    public function callback($method) {
        return $this->getAnnotationValue(self::CALLBACK, $method);
    }

    /**
     * @Errorアノテーション情報を返却する
     * @return Object エラーアノテーションオブジェクト
     */
    public function error() {
        return $this->methods(self::ERROR);
    }

    /**
     * @Databaseアノテーション情報を返却する
     * @return Object データベースアノテーションオブジェクト
     */
    public function database() {
        return $this->classes(self::DATABASE);
    }

    /**
     * @Tableアノテーション情報を返却する
     * @return Object テーブルアノテーションオブジェクト
     */
    public function table() {
        return $this->classes(self::TABLE);
    }

    /**
     * @Propertiesアノテーション情報を返却する
     * @return Object プロパティアノテーションオブジェクト
     */
    public function properties() {
        return $this->classes(self::PROPERTIES);
    }

    /**
     * @SQLアノテーション情報を返却する
     * @return Object SQLアノテーションオブジェクト
     */
    public function sql() {
        return $this->methods(self::SQL);
    }
}