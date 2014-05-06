<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\ExceptionHandler;
use WebStream\Exception\Extend\ValidateException;
use WebStream\Exception\Extend\InvalidRequestException;
use WebStream\Exception\Extend\ForbiddenAccessException;
use WebStream\Exception\Extend\CsrfException;
use WebStream\Exception\Extend\ResourceNotFoundException;

class TestMultipleExceptionHandlerController extends CoreController
{
    public function index1()
    {
        throw new ValidateException();
    }

    public function index2()
    {
        throw new InvalidRequestException();
    }

    public function index3()
    {
        throw new ForbiddenAccessException();
    }

    public function index4()
    {
        throw new CsrfException();
    }

    public function index5()
    {
        throw new ResourceNotFoundException();
    }

    /**
     * @Inject
     * @ExceptionHandler("WebStream\Exception\Extend\ValidateException")
     */
    public function subException($params)
    {
        echo "1";
    }

    /**
     * @Inject
     * @ExceptionHandler("\Exception")
     */
    public function superException($params)
    {
        echo "2";
    }

    /**
     * @Inject
     * @ExceptionHandler({"WebStream\Exception\Extend\InvalidRequestException", "WebStream\Exception\Extend\ForbiddenAccessException"})
     */
    public function multiHandleException($params)
    {
        echo "3";
    }

    /**
     * @Inject
     * @ExceptionHandler({"WebStream\Exception\Extend\CsrfException", "WebStream\Exception\Extend\CsrfException"})
     */
    public function sameHandleException($params)
    {
        echo "4";
    }

    /**
     * @Inject
     * @ExceptionHandler({"WebStream\Exception\Extend\ResourceNotFoundException", "\Exception"})
     */
    public function ancestorHandleException($params)
    {
        echo "5";
    }
}
