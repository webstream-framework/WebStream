<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;

class AfterFilterTest2
{
    /**
     * @Inject
     * @Filter("After")
     */
    public function after1()
    {
        echo "a1";
    }

    /**
     * @Inject
     * @Filter("After")
     */
    public function after2()
    {
        echo "a2";
    }

    public function index()
    {
        echo "i";
    }
}
