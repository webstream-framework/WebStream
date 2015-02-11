<?php
namespace WebStream\Annotation\Base;

use WebStream\Core\CoreInterface;

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
     * @param CoreInterface 注入先インスタンス
     * @param ReflectionProperty リフレクションプロパティオブジェクト
     */
    public function onPropertyInject(CoreInterface &$instance, \ReflectionProperty $property);
}
