<?php
namespace WebStream\Template;

use WebStream\Container\Container;

/**
 * ITemplateEngine
 * @author Ryuichi Tanaka
 * @since 2015/03/18
 * @version 0.7
 */
interface ITemplateEngine
{
    /**
     * constructor
     */
    public function __construct(Container $container);

    /**
     * テンプレートを描画
     * @param array<string> 埋め込みパラメータ
     */
    public function render(array $params);
}
