<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\ExceptionHandler;
use WebStream\Exception\Extend\ClassNotFoundException;
use WebStream\Exception\Extend\ResourceNotFoundException;

class TestDoubleExceptionHandlerController extends CoreController
{
    public function index1()
    {
        throw new ClassNotFoundException();
    }

    public function index2()
    {
        throw new ResourceNotFoundException();
    }

    /**
     * @Inject
     * @ExceptionHandler("WebStream\Exception\Extend\ClassNotFoundException")
     * @ExceptionHandler("WebStream\Exception\Extend\ResourceNotFoundException")
     */
    public function multiHandleException2($params)
    {
        echo "1";
    }
}
