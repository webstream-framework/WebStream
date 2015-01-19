<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;

class TestNoServiceClassController extends CoreController
{
    public function execute()
    {
        echo $this->TestNoServiceClass->get();
    }
}
