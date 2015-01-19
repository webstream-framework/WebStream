<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;

class TestSkipFilterController extends CoreController
{
    /**
     * @Inject
     * @Filter(type="before")
     */
    public function before1()
    {
        echo "b1";
    }

    /**
     * @Inject
     * @Filter(type="before")
     */
    public function before2()
    {
        echo "b2";
    }

    /**
     * @Inject
     * @Filter(type="skip", except="before1")
     */
    public function index1()
    {
        echo "i";
    }

    /**
     * @Inject
     * @Filter(type="skip", except={"before1","before2"})
     */
    public function index2()
    {
        echo "i";
    }
}
