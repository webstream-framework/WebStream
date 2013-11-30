<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;

class TestParentFilterController extends CoreController
{
    /**
     * @Inject
     * @Filter("Before")
     */
    public function before()
    {
        echo "b2";
    }

    /**
     * @Inject
     * @Filter("After")
     */
    public function after()
    {
        echo "a2";
    }
}

class TestOverrideFilterController extends TestParentFilterController
{
    /**
     * @Inject
     * @Filter("Before")
     */
    public function before()
    {
        echo "b1";
    }

    /**
     * @Inject
     * @Filter("After")
     */
    public function after()
    {
        echo "a1";
    }

    public function index()
    {
        echo "i";
    }
}
