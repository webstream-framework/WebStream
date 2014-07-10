<?php
namespace WebStream\Test\TestData\Sample\App\Model;

use WebStream\Core\CoreModel;
use WebStream\Test\TestData\Sample\App\Entity\TestEntity;

class TestCustomDirModel extends CoreModel
{
    public function model1()
    {
        return new TestEntity();
    }
}
