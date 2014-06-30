<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;

class TestExistServiceExistModelExistModelMethodController extends CoreController
{
    public function sendParam()
    {
        $this->TestExistServiceExistModelExistModelMethod->get1("abc");
    }

    public function sendParams()
    {
        $this->TestExistServiceExistModelExistModelMethod->get2("abc", "def");
    }
}
