<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;

class InitializeFilterTest2
{
    /**
     * @Inject
     * @Filter("Initialize")
     */
    public function initialize()
    {
        echo "I";
    }

    /**
     * @Inject
     * @Filter("Initialize")
     */
    public function initialize2()
    {
        echo "I";
    }

    public function index()
    {
        echo "i";
    }
}
