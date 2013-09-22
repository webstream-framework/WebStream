<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;

class FilterOverrideTest1 extends FilterOverrideTest2
{
    /**
     * @Inject
     * @Filter("Before")
     */
    public function before()
    {
        echo "a1";
    }

    public function index()
    {
        echo "i";
    }
}
