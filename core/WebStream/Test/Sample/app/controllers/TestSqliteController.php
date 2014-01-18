<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;

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
        $result = $this->TestSqlite->model3();
        foreach ($result as $value) {
            echo $value["name"];
        }
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
