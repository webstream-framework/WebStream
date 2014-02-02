<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;

class TestExceptOnlyFilterController extends CoreController
{
    /**
     * @Inject
     * @Filter(type="before", only="index", except="index")
     */
    public function before()
    {
    }

    public function index()
    {
    }
}
