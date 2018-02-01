<?php
namespace WebStream\Core;

use WebStream\Container\Container;

/**
 * CoreInterface
 * @author Ryuichi TANAKA.
 * @since 2013/12/09
 * @version 0.4
 */
interface CoreInterface
{
    /**
     * 初期処理
     * @param Container DIContainer
     */
    public function __initialize(Container $container);

    /**
    * カスタムアノテーション情報を設定する
    * @param array<mixed> カスタムアノテーション情報
     */
    public function __customAnnotation(array $annotation);
}
