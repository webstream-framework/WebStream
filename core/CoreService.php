<?php
namespace WebStream;
/**
 * CoreServiceクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/11
 */
class CoreService extends CoreBase {
    /**
     * Controllerから存在しないメソッドが呼ばれたときの処理
     * @param String メソッド名
     * @param Array 引数の配列
     * @return 実行結果
     */
    final public function __call($method, $arguments) {
        // Modelクラス両方にメソッドが存在しなければエラー
        if ($this->__pageName === null || 
            method_exists($this->{$this->__pageName}, $method) === false) {
            $class = $this->__toString();
            throw new MethodNotFoundException("${class}#${method} is not defined.");
        }
        return call_user_func_array(array($this->{$this->__pageName}, $method), $arguments);
    }
    
    /**
     * Serviceクラス全体の初期化
     * @param String ページ名
     */
    final public function __construct(Container $container) {
        parent::__construct($container);
        $this->{$this->__pageName} = $this->__getModel();
        importAll(STREAM_APP_DIR . "/libraries");
    }
}
