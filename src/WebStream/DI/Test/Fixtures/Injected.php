<?php
namespace WebStream\DI\Test;

require_once dirname(__FILE__) . '/../../Injector.php';

use WebStream\DI\Injector;

class Injected
{
    use Injector;

    public function getValue($key)
    {
        return $this->{$key};
    }
}
