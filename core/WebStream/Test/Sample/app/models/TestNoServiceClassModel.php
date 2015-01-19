<?php
namespace WebStream\Test\TestData\Sample\App\Model;

use WebStream\Core\CoreModel;

class TestNoServiceClassModel extends CoreModel
{
    public function get()
    {
        return "no service class";
    }
}
