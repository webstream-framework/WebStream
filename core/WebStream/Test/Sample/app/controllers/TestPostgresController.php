<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\ExceptionHandler;

class TestPostgresController extends CoreController
{
    public function model1()
    {
        $result = $this->TestPostgres->model1();
        foreach ($result as $value) {
            echo $value["name"];
        }
    }

    public function model2()
    {
        $result = $this->TestPostgres->model2();
        foreach ($result as $value) {
            echo $value["name"];
        }
    }

    public function model3()
    {
        $result = $this->TestPostgres->model3();
        foreach ($result as $value) {
            echo $value["name"];
        }
    }

    public function model4()
    {
        $result = $this->TestPostgres->model4();
        echo $result->toArray()[0]["count"];
    }

    public function model5()
    {
        $this->TestPostgres->model5();
    }

    public function model6()
    {
        $result = $this->TestPostgres->model6();
        echo $result->toArray()[0]["count"];
    }

    /**
     * @Inject
     * @ExceptionHandler("WebStream\Exception\Extend\DatabaseException")
     */
    public function handle($params)
    {
        echo $params["class"] . "#" . $params["method"];
    }

    public function prepare()
    {
        $this->TestPostgres->prepare();
    }

    public function clear()
    {
        $this->TestPostgres->clear();
    }
}
