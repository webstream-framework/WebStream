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

    /** Annotationクラスインスタンス */
    private $annotation;
    /** 適用するアクションメソッド名 */
    private $action;

    /**
     * コンストラクタ
     * @param String 適用するコントローラクラス名        
     */
    public function __construct($controller, $action) {
        isset($controller) and parent::__construct(STREAM_CLASSPATH . $controller);
        $this->action = $action;
    }

    /**
     * アノテーション値を返却する    
     * @params String アノテーションマーク
     * @return String or Array アノテーション値またはリスト
     */
    private function getAnnotationValue($mark) {
        $methodAnnotations = $this->methods($mark);
        foreach ($methodAnnotations as $methodAnnotation) {
            if ($methodAnnotation->methodName === $this->action) {
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
     * @return Hash レンダリング情報
     */
    public function render() {
        $annotations = $this->methods(self::RENDER, self::LAYOUT);
        $method = null;
        $templates = array();
        $methods = array();
        $argList = array();

        $methodName = function($mark) {
            return $mark === Injection::LAYOUT ? '__layout' : '__render';
        };
        foreach ($annotations as $annotation) {
            if ($annotation->methodName === $this->action) {
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
     * @return Array 許可されたリクエストメソッドリスト    
     */
    public function request() {
        return $this->getAnnotationValue(self::REQUEST);
    }

    /**
     * @BasicAuthアノテーション情報を返却する    
     * @return String 基本認証設定ファイルパス
     */
    public function basicAuth() {
        return $this->getAnnotationValue(self::BASIC_AUTH);
    }

    /**
     * @Cacheアノテーション情報を返却する
     * @return int キャッシュ有効時間
     */
    public function cache() {
        return $this->getAnnotationValue(self::CACHE);
    }

    /**
     * @Securityアノテーション情報を返却する
     * @return String セキュリティアノテーション値 
     */
    public function security() {
        return $this->getAnnotationValue(self::SECURITY);
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
     * @return String 出力形式名
     */
    public function format() {
        $format = $this->getAnnotationValue(self::FORMAT);
        return $this->getAnnotationValue(self::FORMAT) ?: 'html';
    }

    /**
     * @Callbackアノテーション情報を返却する
     * @return String JSONPコールバック名
     */
    public function callback() {
        return $this->getAnnotationValue(self::CALLBACK);
    }

    /**
     * @Errorアノテーション情報を返却する
     * @return Object エラーアノテーションオブジェクト
     */
    public function error() {
        return $this->methods(self::ERROR);
    }
}