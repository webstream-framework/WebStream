<?php
namespace WebStream\Test\TestData\Sample\App\Helper;

use WebStream\Core\CoreHelper;
use WebStream\Core\CoreService;

class TestHelperHelper extends CoreHelper
{
    public function help1()
    {
        echo "erichika";
    }

    public function help2($name)
    {
        return $name;
    }

    public function help3(CoreService $model)
    {
        return $this->help2($model->getName());
    }

    public function help4(CoreService $model)
    {
        return <<< HELPER
{$model->getName()}
HELPER;
    }

    public function help5()
    {
        return <<< HELPER
<script>alert("xss");</script>
HELPER;
    }

    public function help6(CoreService $model)
    {
        $map = $model->getMap();
        echo $map["name"] . $map["age"];
    }
}
