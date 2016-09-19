<?php
namespace WebStream\Annotation\Base;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Container\Container;

/**
 * IMethod
 * @author Ryuichi TANAKA.
 * @since 2015/02/10
 * @version 0.4
 */
interface IMethod
{
    /**
     * メソッドオブジェクトを注入
     * @param IAnnotatable 注入先インスタンス
     * @param Container 依存コンテナ
     * @param ReflectionMethod リフレクションメソッドオブジェクト
     */
    public function onMethodInject(IAnnotatable &$instance, Container $container, \ReflectionMethod $method);
}
