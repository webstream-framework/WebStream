<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\ExceptionHandler;

class TestMysqlController extends CoreController
{
    public function model1()
    {
        $result = $this->TestMysql->model1();
        foreach ($result as $value) {
            echo $value["name"];
        }
    }

    public function model2()
    {
        $result = $this->TestMysql->model2();
        foreach ($result as $value) {
            echo $value["name"];
        }
    }

    public function model3()
    {
        $result = $this->TestMysql->model3();
        foreach ($result as $value) {
            echo $value["name"];
        }
    }

    public function model4()
    {
        $result = $this->TestMysql->model4();
        echo $result->toArray()[0]["count"];
    }

    public function model5()
    {
        $this->TestMysql->model5();
    }

    public function model6()
    {
        $result = $this->TestMysql->model6();
        echo $result->toArray()[0]["count"];
    }

    public function model7()
    {
        $result = $this->TestMysql->model7();
        foreach ($result as $value) {
            echo $value["name"];
        }
    }

    public function model8()
    {
        $result = $this->TestMysql->model8();
        foreach ($result as $entity) {
            echo $entity->getName();
        }
    }

    public function model9()
    {
        $result = $this->TestMysql->model9();
        foreach ($result as $entity) {
            echo $entity->getValue1();
            echo $entity->getValue2();
            echo $entity->getValue3();
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
        $this->TestMysql->prepare();
    }

    public function clear()
    {
        $this->TestMysql->clear();
    }

    public function prepare2()
    {
        $this->TestMysql->prepare2();
    }

    public function clear2()
    {
        $this->TestMysql->clear2();
    }
}
