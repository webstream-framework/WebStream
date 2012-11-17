<?php
namespace WebStream;
/**
 * Injectionクラス
 * @author Ryuichi TANAKA.
 * @since 2012/11/17
 */
class Injection extends Annotation {
	/** 使用するアノテーション定義 */
	const FORMAT = "@Format";
	const CALLBACK = "@Callback";

	/** Annotationクラスインスタンス */
	private $annotation;
	/** 適用するアクションメソッド名 */
	private $action;

	/**
	 * コンストラクタ
	 * @param String 適用するコントローラクラス名		
	 */
	public function __construct($controller, $action) {
		$this->annotation = new Annotation(STREAM_CLASSPATH . $controller);
		$this->action = $action;
	}

	/**
	 * @Formatアノテーション情報を返却する
	 * @return String 出力形式名
	 */
    public function format() {
        //$annotation = new Annotation(STREAM_CLASSPATH . $this->controller());
        $methodAnnotations = $this->annotation->methods(self::FORMAT);
        $type = "html";
        foreach ($methodAnnotations as $methodAnnotation) {
            if ($methodAnnotation->methodName === $this->action) {
                $type = $methodAnnotation->value;
            }
        }
        return $type;
    }

	/**
	 * @Callbackアノテーション情報を返却する
	 * @return String JSONPコールバック名
	 */
    public function callback() {
        //$annotation = new Annotation(STREAM_CLASSPATH . $this->controller());
        $methodAnnotations = $this->annotation->methods(self::CALLBACK);
        foreach ($methodAnnotations as $methodAnnotation) {
            if ($methodAnnotation->methodName === $this->action {
                return $methodAnnotation->value;
            }
        }
    }
}