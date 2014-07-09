<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;

class TestCustomDirController extends CoreController
{
    public function fromController()
    {
        $obj = $this->TestCustomDir->service1();
        echo get_class($obj);
    }

    public function customDirFromService()
    {
        echo "test1";
    }

    public function customDirFromModel()
    {
        echo "test1";
    }

}
