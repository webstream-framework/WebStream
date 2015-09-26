<?php
namespace WebStream\Test\TestData\Sample\App\Service;

use WebStream\Core\CoreService;

class TestViewModelInServiceService extends CoreService
{
    private $name;

    public function service1()
    {
        $this->honoka = "honoka";
    }

    public function service2()
    {
        $this->name = "kotori";
    }
}
