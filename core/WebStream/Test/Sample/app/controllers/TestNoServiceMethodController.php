<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;

class TestNoServiceMethodController extends CoreController
{
    public function execute()
    {
        echo $this->TestNoServiceMethod->get();
    }
}
