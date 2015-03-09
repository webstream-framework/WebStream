<?php
namespace WebStream\Annotation\Base;

use WebStream\Core\CoreInterface;
use WebStream\Module\Container;

/**
 * IMethods
 * @author Ryuichi TANAKA.
 * @since 2015/02/10
 * @version 0.4
 */
interface IMethods
{
    /**
     * メソッドオブジェクトを注入
     * @param CoreInterface 注入先インスタンス
     * @param Container 依存コンテナ
     * @param ReflectionMethod リフレクションメソッドオブジェクト
     */
    public function onMethodInject(CoreInterface &$instance, Container $container, \ReflectionMethod $method);
}
