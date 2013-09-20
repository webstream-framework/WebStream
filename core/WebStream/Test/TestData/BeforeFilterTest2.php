<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;

class BeforeFilterTest2
{
    /**
     * @Inject
     * @Filter("Before")
     */
    public function before1()
    {
        echo "b1";
    }

    /**
     * @Inject
     * @Filter("Before")
     */
    public function before2()
    {
        echo "b2";
    }

    public function index()
    {
        echo "i";
    }
}
