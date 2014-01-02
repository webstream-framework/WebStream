<?php
namespace WebStream\Test\TestData\Sample\App\Model;

use WebStream\Core\CoreModel;

class TestExistServiceExistModelExistModelMethodModel extends CoreModel
{
    public function get1($arg1)
    {
        echo $arg1;
    }

    public function get2($arg1, $arg2)
    {
        echo $arg1 . $arg2;
    }
}
