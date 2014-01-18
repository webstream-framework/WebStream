<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\ExceptionHandler;

class TestDatabaseError2Controller extends CoreController
{
    public function model1()
    {
        $result = $this->TestDatabaseError2->model1();
    }

    /**
     * @Inject
     * @ExceptionHandler("WebStream\Exception\DatabaseException")
     */
    public function handle($params)
    {
        echo $params["class"] . "#" . $params["method"];
    }
}
