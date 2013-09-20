<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;

class BeforeFilterTest1
{
    /**
     * @Inject
     * @Filter("Before")
     */
    public function before()
    {
        echo "b";
    }

    public function index()
    {
        echo "i";
    }
}
