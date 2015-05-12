<?php
namespace WebStream\Test\TestData\Sample\App\Helper;

use WebStream\Core\CoreHelper;

class TestBasicTemplateWithHelperHelper extends CoreHelper
{
    public function helper1()
    {
        echo "%T{index2_2.tmpl}";
    }

    public function helper2()
    {
        echo "%T{index3_2.tmpl}";
    }

    public function helper3()
    {
        echo "%T{index3_3.tmpl}";
    }
}
