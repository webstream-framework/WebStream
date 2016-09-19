<?php
namespace WebStream\Annotation\Base;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Container\Container;

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
     * @param IAnnotatable 注入先インスタンス
     * @param Container 依存コンテナ
     * @param ReflectionClass リフレクションクラスオブジェクト
     */
    public function onClassInject(IAnnotatable &$instance, Container $container, \ReflectionClass $class);
}
