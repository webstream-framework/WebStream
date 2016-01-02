<?php
namespace WebStream\DI;

use WebStream\Module\PropertyProxy;

/**
 * Injector
 * @author Ryuichi TANAKA.
 * @since 2015/12/26
 * @version 0.7
 */
trait Injector
{
    use PropertyProxy;

    /**
     * オブジェクトを注入する
     * @param string プロパティ名
     * @param mixed オブジェクト
     * @return Injector
     */
    public function inject($name, $object)
    {
        $this->{$name} = $object;

        return $this;
    }
}
