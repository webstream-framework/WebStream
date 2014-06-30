<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;

class TestOverrideParentFilterController extends CoreController
{
    /**
     * @Inject
     * @Filter(type="before")
     */
    public function before()
    {
        echo "b2";
    }

    /**
     * @Inject
     * @Filter(type="after")
     */
    public function after()
    {
        echo "a2";
    }
}

class TestOverrideFilterController extends TestOverrideParentFilterController
{
    /**
     * @Inject
     * @Filter(type="before")
     */
    public function before()
    {
        echo "b1";
    }

    /**
     * @Inject
     * @Filter(type="after")
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
