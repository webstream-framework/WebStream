<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\ExceptionHandler;

class TestSqliteController extends CoreController
{
    public function model1()
    {
        $result = $this->TestSqlite->model1();
        foreach ($result as $value) {
            echo $value["name"];
        }
    }

    public function model2()
    {
        $result = $this->TestSqlite->model2();
        foreach ($result as $value) {
            echo $value["name"];
        }
    }

    public function model3()
    {
        $this->TestSqlite->model3();
    }

    public function model4()
    {
        $result = $this->TestSqlite->model4();
        foreach ($result as $value) {
            echo $value["name"];
        }
    }

    public function model5()
    {
        $result = $this->TestSqlite->model5();
        foreach ($result as $entity) {
            echo $entity->getName();
        }
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
        $this->TestSqlite->prepare();
    }

    public function clear()
    {
        $this->TestSqlite->clear();
    }
}
