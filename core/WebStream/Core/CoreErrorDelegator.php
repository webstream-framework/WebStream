<?php
namespace WebStream\Core;

use WebStream\Module\Container;
use WebStream\Module\Logger;
use WebStream\Exception\Extend\ClassNotFoundException;

/**
 * CoreErrorDelegatorクラス
 * @author Ryuichi TANAKA.
 * @since 2011/11/30
 * @version 0.4
 */
class CoreErrorDelegator implements CoreInterface
{
    /** エラーメッセージ */
    private $errorMessage;

    /**
     * {@inheritdoc}
     */
    public function __construct(Container $container)
    {
        $this->errorMessage = $container->errorMessage;
    }

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        Logger::debug("Delegate core layer error.");
    }

    /**
     * 例外を引き起こすデリゲートメソッド
     */
    public function __call($name, $arguments)
    {
        // メソッド呼び出しがあった時点ですべて例外呼び出しする
        // 指定された引数は使用しない
        throw new ClassNotFoundException($this->errorMessage);
    }
}
