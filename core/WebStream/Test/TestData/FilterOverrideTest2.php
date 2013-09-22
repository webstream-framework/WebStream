<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;

class FilterOverrideTest2
{
    /**
     * @Inject
     * @Filter("Before")
     */
    public function before()
    {
        echo "a2";
    }
}
