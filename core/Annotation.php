<?php
namespace WebStream;
/**
 * アノテーションクラス
 * @author Ryuichi TANAKA.
 * @since 2012/08/30
 */
class Annotation {
    /** インジェクションポイント */
    const REGEX_INJECT = '/@Inject[\n|\r|\r\n]+/';
    /** リフレクションクラスインスタンス */
    private $refClass;
    
    /**
     * コンストラクタ
     * @param String クラス名
     */
    public function __construct($className) {
        $this->initClass($className);
    }
    
    /**
     * リフレクションクラスを初期化
     * @param String クラス名
     */
    private function initClass($className) {
        $this->refClass = new \ReflectionClass($className);
    }
    
    /**
     * クラスアノテーションを取得
     * @param String アノテーションマーク
     * @return Array アノテーションリスト
     */
    public function classes($annotation) {
        $class = $this->refClass;
        $classList = array();
        while ($class) {
            $docComment = $class->getDocComment();
            if (preg_match(self::REGEX_INJECT, $docComment)) {
                if (preg_match("/($annotation)\((.*?)\)/", $docComment, $matches)) {
                    $values = preg_split("/,/", preg_replace("/\"|\'|\s/", '', $matches[2]));
                    for ($i = 0; $i < count($values); $i++) {
                        $cls = new \stdClass();
                        $cls->className = $class->getName();
                        $cls->value = $values[$i];
                        $cls->index = $i;
                        $cls->name = $matches[1];
                        $classList[] = $cls;
                    }
                }
            }
            $class = $class->getParentClass();
        }
        return $classList;
    }
    
    /**
     * メソッドアノテーション
     * @param String アノテーションマーク
     * @return Array アノテーションリスト
     */
    public function methods() {
        $annotations = func_get_args();
        $regexp = "(" . implode('|', func_get_args()) . ")";
        $class = $this->refClass;
        $methodList = array();
        while ($class) {
            foreach ($class->getMethods() as $method) {
                if ($class->getName() !== $method->getDeclaringClass()->getName()) break;
                $docComment = $method->getDocComment();
                if (preg_match(self::REGEX_INJECT, $docComment)) {
                    if (preg_match_all("/$regexp\((.*?)\)/", $docComment, $matches)) {
                        for ($i = 0; $i < count($matches[2]); $i++) {
                            $match = $matches[2][$i];
                            $values = preg_split("/,/", preg_replace("/\"|\'|\s/", '', $match));
                            for ($j = 0; $j < count($values); $j++) {
                                $cls = new \stdClass();
                                $cls->methodName = $method->getName();
                                $cls->className = $method->getDeclaringClass()->getName();
                                $cls->value = $values[$j];
                                $cls->index = $j;
                                $cls->name = $matches[1][$j];
                                $methodList[] = $cls;
                            }
                        }
                    }
                }
            }
            $class = $class->getParentClass();
        }

        return $methodList;
    }
}
