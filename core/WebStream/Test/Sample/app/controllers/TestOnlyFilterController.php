<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;

class TestOnlyFilterController extends CoreController
{
    /**
     * @Inject
     * @Filter(type="before", only="index")
     */
    public function before()
    {
        echo "b";
    }

    /**
     * @Inject
     * @Filter(type="after", only="index3")
     */
    public function after()
    {
        echo "a";
    }

    public function index()
    {
        echo "i1";
    }

    public function index2()
    {
        echo "i2";
    }

    public function index3()
    {
        echo "i3";
    }

    public function index4()
    {
        echo "i4";
    }
}