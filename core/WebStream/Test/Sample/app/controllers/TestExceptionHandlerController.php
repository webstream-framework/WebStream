<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\ExceptionHandler;
use WebStream\Exception\ApplicationException;
use WebStream\Exception\Extend\ValidateException;
use WebStream\Exception\Extend\ForbiddenAccessException;
use WebStream\Exception\Extend\SessionTimeoutException;
use WebStream\Exception\Extend\InvalidRequestException;
use WebStream\Exception\Extend\CsrfException;
use WebStream\Exception\Extend\ResourceNotFoundException;
use WebStream\Exception\Extend\ClassNotFoundException;
use WebStream\Exception\Extend\MethodNotFoundException;
use WebStream\Exception\Extend\AnnotationException;
use WebStream\Exception\Extend\RouterException;

class TestExceptionHandlerController extends CoreController
{
    public function index1()
    {
        throw new ValidateException();
    }

    public function index2()
    {
        throw new ForbiddenAccessException();
    }

    public function index3()
    {
        throw new SessionTimeoutException();
    }

    public function index4()
    {
        throw new InvalidRequestException();
    }

    public function index5()
    {
        throw new CsrfException();
    }

    public function index6()
    {
        throw new ResourceNotFoundException();
    }

    public function error1()
    {
        throw new ApplicationException();
    }

    public function error2()
    {
        throw new ClassNotFoundException();
    }

    public function error3()
    {
        throw new MethodNotFoundException();
    }

    public function error4()
    {
        throw new AnnotationException();
    }

    public function error5()
    {
        throw new RouterException();
    }

    /**
     * @Inject
     * @ExceptionHandler("WebStream\Exception\Extend\ValidateException")
     */
    public function validateError($params)
    {
        echo "validator error";
    }

    /**
     * @Inject
     * @ExceptionHandler("WebStream\Exception\Extend\ForbiddenAccessException")
     */
    public function forbiddenAccessError($params)
    {
        echo "forbidden access error";
    }

    /**
     * @Inject
     * @ExceptionHandler("WebStream\Exception\Extend\SessionTimeoutException")
     */
    public function sessionTimeoutError($params)
    {
        echo "session timeout error";
    }

    /**
     * @Inject
     * @ExceptionHandler("WebStream\Exception\Extend\InvalidRequestException")
     */
    public function invalidRequestError($params)
    {
        echo "invalid request error";
    }

    /**
     * @Inject
     * @ExceptionHandler("WebStream\Exception\Extend\CsrfException")
     */
    public function csrfError($params)
    {
        echo "csrf error";
    }

    /**
     * @Inject
     * @ExceptionHandler("WebStream\Exception\Extend\ResourceNotFoundException")
     */
    public function resourceNotfoundError($params)
    {
        echo "resource notfound error";
    }

    /**
     * @Inject
     * @ExceptionHandler("WebStream\Exception\Extend\ClassNotFoundException")
     */
    public function uncatchedError2($params)
    {
        echo "classnotfound error";
    }

    /**
     * @Inject
     * @ExceptionHandler("WebStream\Exception\Extend\MethodNotFoundException")
     */
    public function uncatchedError3($params)
    {
        echo "methodnotfound error";
    }

    /**
     * @Inject
     * @ExceptionHandler("WebStream\Exception\Extend\AnnotationException")
     */
    public function uncatchedError4($params)
    {
        echo "annotation error";
    }

    /**
     * @Inject
     * @ExceptionHandler("WebStream\Exception\Extend\RouterException")
     */
    public function uncatchedError5($params)
    {
        echo "router error";
    }
}
