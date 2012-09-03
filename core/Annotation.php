<?php
/**
 * アノテーションクラス
 * @author Ryuichi TANAKA.
 * @since 2012/08/30
 */
class Annotation {
    /** インジェクションポイント */
    const REGEX_INJECT = '/@Inject\n+/';
    /** リフレクションクラス */
    private $refClass;
    
    /**
     * コンストラクタ
     * @param String クラス名
     */
    public function __construct($className) {
        $this->refClass = new ReflectionClass($className);
    }
    
    /**
     * クラスアノテーションを取得
     * @param String アノテーションマーク
     * @return Array アノテーションリスト
     */
    public function classes($annotation) {
        $classList = array();
        while ($this->refClass) {
            $docComment = $this->refClass->getDocComment();
            if (preg_match(self::REGEX_INJECT, $docComment)) {
                if (preg_match("/$annotation\((.*?)\)/", $docComment, $matches)) {
                    $values = preg_split("/,/", preg_replace("/\"|\'|\s/", '', $matches[1]));
                    foreach ($values as $value) {
                        $cls = new stdClass();
                        $cls->className = $this->refClass->getName();
                        $cls->value = $value;
                        $classList[] = $cls;
                    }
                }
            }
            $this->refClass = $this->refClass->getParentClass();
        }
        return $classList;
    }
    
    /**
     * メソッドアノテーション
     * @param String アノテーションマーク
     * @return Array アノテーションリスト
     */
    public function methods($annotation) {
        $methodList = array();
        while ($this->refClass) {
            foreach ($this->refClass->getMethods() as $method) {
                if ($this->refClass->getName() !== $method->getDeclaringClass()->getName()) break;
                $docComment = $method->getDocComment();
                if (preg_match(self::REGEX_INJECT, $docComment)) {
                    if (preg_match("/$annotation\((.*?)\)/", $docComment, $matches)) {
                        $values = preg_split("/,/", preg_replace("/\"|\'|\s/", '', $matches[1]));
                        foreach ($values as $value) {
                            $cls = new stdClass();
                            $cls->methodName = $method->getName();
                            $cls->className = $method->getDeclaringClass()->getName();
                            $cls->value = $value;
                            $methodList[] = $cls;
                        }
                    }
                }
            }
            $this->refClass = $this->refClass->getParentClass();
        }
        return $methodList;
    }
}
