<?php
namespace WebStream;
/**
 * CoreHelperクラス
 * @author Ryuichi TANAKA.
 * @since 2012/04/28
 */
class CoreHelper extends CoreBase {
    /**
     * コンストラクタ
     * @param String ページ名
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * メソッド呼び出しの初期化処理を実行する
     * @param String メソッド名
     * @param Array メソッドの引数
     */
    public function __initialize($method, $args) {
        // メソッド名を安全な値に置換
        $methodName = Utility::snake2lcamel(safetyOut($method));
        // 引数を安全な値に置換
        for ($i = 0; $i < count($args); $i++) {
            $args[$i] = safetyOut($args[$i]);
        }

        $method = new \ReflectionMethod($this->__toString(), $methodName);
        echo $method->invokeArgs($this, $args);
    }
}
