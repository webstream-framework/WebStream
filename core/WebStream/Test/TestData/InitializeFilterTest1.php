<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;

class InitializeFilterTest1
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
     * @Filter("Before")
     */
    public function before()
    {
        echo "a";
    }

    public function index()
    {
        echo "i";
    }
}
