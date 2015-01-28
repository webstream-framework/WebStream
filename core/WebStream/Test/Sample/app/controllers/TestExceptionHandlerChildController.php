<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Annotation\Inject;
use WebStream\Annotation\ExceptionHandler;
use WebStream\Exception\Extend\ValidateException;
use WebStream\Exception\Extend\ForbiddenAccessException;

class TestExceptionHandlerChildController extends TestExceptionHandlerParentController
{
    public function index1()
    {
        throw new ForbiddenAccessException();
    }

    public function index2()
    {
        throw new ValidateException();
    }

    /**
     * @Inject
     * @ExceptionHandler("WebStream\Exception\Extend\ValidateException")
     */
    public function childHandleException($params)
    {
        echo "child";
    }
}
