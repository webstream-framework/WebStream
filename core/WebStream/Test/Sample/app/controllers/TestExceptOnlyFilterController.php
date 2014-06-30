<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;

class TestExceptOnlyFilterController extends CoreController
{
    /**
     * @Inject
     * @Filter(type="before", except="index", only="index")
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
