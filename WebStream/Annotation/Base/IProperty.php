<?php
namespace WebStream\Annotation\Base;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Module\Container;

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
     * @param WebStream\Annotation\Base\IAnnotatable 注入先インスタンス
     * @param WebStream\Module\Container 依存コンテナ
     * @param ReflectionProperty リフレクションプロパティオブジェクト
     */
    public function onPropertyInject(IAnnotatable &$instance, Container $container, \ReflectionProperty $property);
}
