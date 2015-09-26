<?php
namespace WebStream\Test\TestData\Sample\App\Model;

use WebStream\Core\CoreModel;

class TestViewModelInModelModel extends CoreModel
{
    private $name;

    public function model1()
    {
        $this->honoka = "honoka";
    }

    public function model2()
    {
        $this->name = "kotori";
    }
}
