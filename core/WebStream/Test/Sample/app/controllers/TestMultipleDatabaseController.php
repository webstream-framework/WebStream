<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;

class TestMultipleDatabaseController extends CoreController
{
    public function model1()
    {
        $this->TestMultipleDatabase->model1();
    }
}
