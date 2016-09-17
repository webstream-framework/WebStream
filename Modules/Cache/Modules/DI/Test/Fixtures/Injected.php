<?php
namespace WebStream\DI\Test;

require_once dirname(__FILE__) . '/../../Injector.php';

use WebStream\DI\Injector;

/**
 * InjectorTest
 * @author Ryuichi TANAKA.
 * @since 2016/09/11
 * @version 0.7
 */
class Injected
{
    use Injector;

    public function getValue($key)
    {
        return $this->{$key};
    }
}
