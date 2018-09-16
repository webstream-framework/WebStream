<?php
namespace WebStream\Annotation\Base;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Container\Container;

/**
 * IProperty
 * @author Ryuichi TANAKA.
 * @since 2015/02/10
 * @version 0.4
 */
interface IProperty
{
    /**
     * プロパティオブジェクトを注入
     * @param IAnnotatable 注入先インスタンス
     * @param ReflectionProperty リフレクションプロパティオブジェクト
     * @param Container 依存コンテナ
     */
    public function onPropertyInject(IAnnotatable $instance, \ReflectionProperty $property, Container $container);
}
