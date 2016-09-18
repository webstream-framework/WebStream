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
     * @param Container 依存コンテナ
     * @param ReflectionProperty リフレクションプロパティオブジェクト
     */
    public function onPropertyInject(IAnnotatable &$instance, Container $container, \ReflectionProperty $property);
}
