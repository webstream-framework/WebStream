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

    public function model6()
    {
        $result = $this->TestSqlite->model6();
        foreach ($result as $entity) {
            echo $entity->getValue1();
            echo $entity->getValue2();
            echo $entity->getValue3();
        }
    }

    public function model7()
    {
        $result = $this->TestSqlite->model7();
        foreach ($result as $entity) {
            echo $entity->getName();
            echo $entity->getValue1();
            echo $entity->getValue2();
            echo $entity->getValue3();
        }
    }

    public function model8()
    {
        $result = $this->TestSqlite->model8();
        foreach ($result as $entity) {
            echo $entity->getId1();
            echo $entity->getId2();
        }
    }

    public function model9()
    {
        $result = $this->TestSqlite->model9();
        foreach ($result as $entity) {
            echo gettype($entity->getId());
            echo gettype($entity->getName());
            echo gettype($entity->getCreatedAt());
            echo gettype($entity->getCreatedAtTime());
            echo gettype($entity->getCreatedAtDate());
            echo gettype($entity->getBigintNum());
            echo gettype($entity->getSmallintNum());
        }
    }

    public function model10()
    {
        $results = $this->TestSqlite->model10();
        foreach ($results[0] as $value) {
            echo $value["name"];
        }
        foreach ($results[1] as $entity) {
            echo gettype($entity->getName());
        }
    }

    public function model11()
    {
        $results = $this->TestSqlite->model11();
        foreach ($results[0] as $value) {
            echo $value["name"];
        }
        foreach ($results[1] as $entity) {
            echo gettype($entity->getName());
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

    public function prepare2()
    {
        $this->TestSqlite->prepare2();
    }

    public function clear2()
    {
        $this->TestSqlite->clear2();
    }

    public function prepare3()
    {
        $this->TestSqlite->prepare3();
    }

    public function clear3()
    {
        $this->TestSqlite->clear3();
    }
}
