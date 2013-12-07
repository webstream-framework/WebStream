<?php
namespace WebStream\Test\TestData\Sample\App\Helper;

use WebStream\Core\CoreHelper;

class TestHelperHelper extends CoreHelper
{
    public function help1()
    {
        echo "erichika";
    }

    public function help2($name)
    {
        echo $name;
    }

    public function help3($name)
    {
        return '!{help2($name)}';
    }

    public function help4($name)
    {
        return <<< HELPER
$name
HELPER;
    }

    public function help5()
    {
        return <<< HELPER
<script>alert("xss");</script>
HELPER;
    }

    public function help6($name, $age)
    {
        echo $name . $age;
    }
}
