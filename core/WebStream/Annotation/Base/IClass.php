<?php
namespace WebStream\Annotation\Base;

use WebStream\Core\CoreInterface;
use WebStream\Module\Container;

/**
 * IClass
 * @author Ryuichi TANAKA.
 * @since 2015/02/10
 * @version 0.4
 */
interface IClass
{
    /**
     * クラスオブジェクトを注入
     * @param CoreInterface 注入先インスタンス
     * @param Container 依存コンテナ
     * @param ReflectionClass リフレクションクラスオブジェクト
     */
    public function onClassInject(CoreInterface &$instance, Container $container, \ReflectionClass $class);
}
