<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\ExceptionHandler;
use WebStream\Exception\ValidateException;

class TestMultipleExceptionHandlerController extends CoreController
{
    public function index1()
    {
        throw new ValidateException();
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
}
