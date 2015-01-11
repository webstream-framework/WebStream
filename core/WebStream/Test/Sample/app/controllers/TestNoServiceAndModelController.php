<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;

class TestNoServiceAndModelController extends CoreController
{
    public function execute()
    {
        $this->TestNoServiceAndModel->get();
    }
}
