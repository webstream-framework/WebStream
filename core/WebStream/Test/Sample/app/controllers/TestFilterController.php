<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;

class TestFilterController extends CoreController
{
    /**
     * @Inject
     * @Filter("Before")
     */
    public function before()
    {
        echo "b";
    }

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
