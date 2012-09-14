<?php
namespace WebStream;
/**
 * Validatorクラス
 * @author Ryuichi TANAKA.
 * @since 2012/09/13
 */
class Validator {
    /** バリデーションルール */
    private static $rules;
    
    /**
     * コンストラクタ
     */
    public function __construct() {
        $this->validate();
    }
    
    /**
     * バリデーションルールを設定する
     * @param Hash バリデーションルール定義
     */
    public static function setRule($rules) {
        self::$rules = $rules;
    }
    
    /**
     * バリデーションルールを検証する
     */
    private function validate() {
        foreach (self::$rules as $ca => $rules) {
            // CA部分('controller#action')が間違っている(存在しない)場合はエラーとする
            $ca = preg_split('/#/', $ca);
            // インポートが失敗した場合、または、クラスの呼び出しに失敗した場合エラー
            $controller = Utility::snake2ucamel($ca[0]) . "Controller";
            // Controllerクラスが存在しなければ例外
            if (!import(STREAM_APP_DIR . "/controllers/" . $controller) || !class_exists(STREAM_CLASSPATH . $controller)) {
                $errorMsg = "Validation rule error. Controller name is invalid: $ca[0]";
                throw new ClassNotFoundException($errorMsg);
            }
            // Actionメソッドが存在しなければ例外
            $class = new \ReflectionClass(STREAM_CLASSPATH . $controller);
            if (!$class->hasMethod($ca[1])) {
                $errorMsg = "Validation rule error. Action name is invalid: $ca[1]";
                throw new MethodNotFoundException($errorMsg);
            }
        }
        
    }
    
    
    
}
