<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;

class AfterFilterTest1
{
    /**
     * @Inject
     * @Filter("After")
     */
    public function after()
    {
        echo "a";
    }

    public function index()
    {
        echo "i";
    }
}
