<?php
/**
 * CoreModelクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/11
 */
class CoreModel {
    /** DBインスタンスを格納するメンバ変数 */
    protected $db;
    
    /**
     * Controllerから存在しないメソッドが呼ばれたときの処理
     * Modelは移譲先がないので必ず例外にする
     * @param String メソッド名
     * @param Array 引数の配列
     */
    final public function __call($method, $arguments) {
        $class = get_class($this);
        throw new MethodNotFoundException("${class}#${method} is not defined.");
    }
    
    /**
     * Modelクラス全体の初期化
     */
    final public function __construct() {
        $this->db = Database::manager();
    }
}
