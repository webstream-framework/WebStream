<?php
namespace WebStream\Test\TestData\Sample\App\Model;

use WebStream\Core\CoreModel;

class TestNoServiceMethodModel extends CoreModel
{
    public function get()
    {
        return "no service method";
    }
}
