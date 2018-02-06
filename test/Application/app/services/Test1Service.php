<?php
namespace WebStream\Test\Service;

use WebStream\Core\CoreService;
use WebStream\Annotation\Attributes\ExceptionHandler;
use WebStream\Annotation\Attributes\Filter;

class Test1Service extends CoreService
{
    /**
     * @Filter(type="before")
     */
    public function init()
    {
        echo "init";
    }

    public function test1()
    {
        return $this->Test1->test1();
    }

    /**
     * @ExceptionHandler("\Exception")
     */
    public function error()
    {
        echo "error";
    }
}
