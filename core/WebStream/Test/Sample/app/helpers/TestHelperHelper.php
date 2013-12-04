<?php
namespace WebStream\Test\TestData\Sample\App\Helper;

use WebStream\Core\CoreHelper;

class TestHelperHelper extends CoreHelper
{
    public function test1($name)
    {
        return '!{test2($name)}';
    }

    public function test2($name)
    {
        echo $name;
    }
}
