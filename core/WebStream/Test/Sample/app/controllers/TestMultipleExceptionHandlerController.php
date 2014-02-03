<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\ExceptionHandler;
use WebStream\Exception\ValidateException;
use WebStream\Exception\InvalidRequestException;
use WebStream\Exception\ForbiddenAccessException;
use WebStream\Exception\CsrfException;
use WebStream\Exception\ResourceNotFoundException;

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
     * @ExceptionHandler("WebStream\Exception\ValidateException")
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
     * @ExceptionHandler({"WebStream\Exception\InvalidRequestException", "WebStream\Exception\ForbiddenAccessException"})
     */
    public function multiHandleException($params)
    {
        echo "3";
    }

    /**
     * @Inject
     * @ExceptionHandler({"WebStream\Exception\CsrfException", "WebStream\Exception\CsrfException"})
     */
    public function sameHandleException($params)
    {
        echo "4";
    }

    /**
     * @Inject
     * @ExceptionHandler({"WebStream\Exception\ResourceNotFoundException", "\Exception"})
     */
    public function ancestorHandleException($params)
    {
        echo "5";
    }
}
