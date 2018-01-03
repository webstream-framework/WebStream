<?php
namespace WebStream\DI\Test;

require_once dirname(__FILE__) . '/../../Injector.php';

use WebStream\DI\Injector;

class Sample1
{
}

class Sample2
{
}

class Sample3 extends Sample1
{
}

class StrictInjected
{
    use Injector;

    /**
     * @var Sample1
     */
    private $value;

    public function getValue()
    {
        return $this->value;
    }
}
