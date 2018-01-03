<?php
namespace WebStream\Annotation\Base;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Container\Container;

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
     * @param IAnnotatable 注入先インスタンス
     * @param ReflectionMethod リフレクションメソッドオブジェクト
     * @param Container 依存コンテナ
     */
    public function onMethodInject(IAnnotatable $instance, \ReflectionMethod $method, Container $container);
}
