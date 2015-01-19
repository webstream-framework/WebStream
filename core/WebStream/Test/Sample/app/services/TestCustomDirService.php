<?php
namespace WebStream\Test\TestData\Sample\App\Service;

use WebStream\Core\CoreService;
use WebStream\Test\TestData\Sample\App\Entity\TestEntity;

class TestCustomDirService extends CoreService
{
    public function service1()
    {
        return new TestEntity();
    }
}
