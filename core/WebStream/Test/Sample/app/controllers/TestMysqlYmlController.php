<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;

class TestMysqlYmlController extends CoreController
{
    public function model1()
    {
        $result = $this->TestMysqlYml->model1();
        foreach ($result as $value) {
            echo $value["name"];
        }
    }

    public function prepare()
    {
        $this->TestMysqlYml->prepare();
    }

    public function clear()
    {
        $this->TestMysqlYml->clear();
    }
}
