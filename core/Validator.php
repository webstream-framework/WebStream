<?php
namespace WebStream;

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
            $controller = Utility::snake2ucamel($ca[0]);
            //echo $controller;
            //if (import(STREAM_APP_DIR . "/controllers/" . $this->controller())) {
                
            //}
        }
        
    }
    
    
    
}
