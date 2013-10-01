<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;

class FilterExceptionTest1
{
    /**
     * @Inject
     * @Filter("test", "test")
     */
    public function after()
    {
        echo "a";
    }
}
