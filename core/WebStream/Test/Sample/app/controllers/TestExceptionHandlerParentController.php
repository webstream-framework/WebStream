<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\ExceptionHandler;

class TestExceptionHandlerParentController extends CoreController
{
    /**
     * @Inject
     * @ExceptionHandler("\Exception")
     */
    public function parentHandleException($params)
    {
        echo "parent";
    }

}
