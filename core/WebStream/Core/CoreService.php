<?php
namespace WebStream\Core;

use WebStream\Module\Container;
use WebStream\Module\Utility;
use WebStream\Module\ClassLoader;
use WebStream\Exception\MethodNotFoundException;

/**
 * CoreService
 * @author Ryuichi TANAKA.
 * @since 2011/09/11
 * @version 0.4.1
 */
class CoreService
{
    use Utility;

    /** coreDelegator */
    private $coreDelegator;

    /**
     * コンストラクタ
     * @param String ページ名
     */
    public function __construct(Container $container)
    {
        $this->coreDelegator = $container->coreDelegator;
        $this->{$this->coreDelegator->getPageName()} = $this->coreDelegator->getModel();
        $classLoader = new ClassLoader();
        $classLoader->importAll(STREAM_APP_DIR . "/libraries");
    }

    /**
     * Controllerから存在しないメソッドが呼ばれたときの処理
     * @param string メソッド名
     * @param array 引数の配列
     * @return 実行結果
     */
    final public function __call($method, $arguments)
    {
        $pageName = $this->coreDelegator->getPageName();
        if (method_exists($this->{$pageName}, $method) === false) {
            $class = get_class($this);
            throw new MethodNotFoundException("${class}#${method} is not defined.");
        }

        return call_user_func_array([$this->{$pageName}, $method], $arguments);
    }
}
