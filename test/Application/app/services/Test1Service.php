<?php
namespace WebStream\Test\Service;

use WebStream\Core\CoreService;
use WebStream\Annotation\Attributes\ExceptionHandler;
use WebStream\Util\PropertyProxy;

class Test1Service extends CoreService
{
    use PropertyProxy;

    public function test1()
    {
        $this->result = $this->Test1->test1();
    }

    /**
     * @ExceptionHandler("\Exception")
     */
    public function error()
    {
        echo "error";
    }
}
