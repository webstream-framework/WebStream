<?php
namespace WebStream\Test\TestData\Sample\App\Helper;

use WebStream\Core\CoreHelper;

class TestCustomDirHelper extends CoreHelper
{
    public function show()
    {
        return get_class(new \WebStream\Test\TestData\Sample\App\Entity\TestEntity());
    }
}
