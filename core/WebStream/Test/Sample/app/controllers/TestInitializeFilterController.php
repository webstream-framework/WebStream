<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;

class TestInitializeFilterController extends CoreController
{
    /**
     * @Inject
     * @Filter(type="initialize")
     */
    public function initialize()
    {
    }

    public function index()
    {
    }
}
