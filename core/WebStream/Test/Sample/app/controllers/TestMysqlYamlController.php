<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;

class TestMysqlYamlController extends CoreController
{
    public function model1()
    {
        $result = $this->TestMysqlYaml->model1();
        foreach ($result as $value) {
            echo $value["name"];
        }
    }

    public function prepare()
    {
        $this->TestMysqlYaml->prepare();
    }

    public function clear()
    {
        $this->TestMysqlYaml->clear();
    }
}
