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

    public function model7()
    {
        $result = $this->TestPostgres->model7();
        foreach ($result as $value) {
            echo $value["name"];
        }
    }

    public function model8()
    {
        $result = $this->TestPostgres->model8();
        foreach ($result as $entity) {
            echo $entity->getName();
        }
    }

    public function model9()
    {
        $result = $this->TestPostgres->model9();
        foreach ($result as $entity) {
            echo $entity->getValue1();
            echo $entity->getValue2();
            echo $entity->getValue3();
        }
    }

    public function model10()
    {
        $result = $this->TestPostgres->model10();
        foreach ($result as $entity) {
            echo $entity->getName();
            echo $entity->getValue1();
            echo $entity->getValue2();
            echo $entity->getValue3();
        }
    }

    public function model11()
    {
        $result = $this->TestPostgres->model11();
        foreach ($result as $entity) {
            echo $entity->getId1();
            echo $entity->getId2();
        }
    }

    public function model12()
    {
        $result = $this->TestPostgres->model12();
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

    public function model13()
    {
        $results = $this->TestPostgres->model13();
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
        $this->TestPostgres->prepare();
    }

    public function clear()
    {
        $this->TestPostgres->clear();
    }

    public function prepare2()
    {
        $this->TestPostgres->prepare2();
    }

    public function clear2()
    {
        $this->TestPostgres->clear2();
    }

    public function prepare3()
    {
        $this->TestPostgres->prepare3();
    }

    public function clear3()
    {
        $this->TestPostgres->clear3();
    }
}
