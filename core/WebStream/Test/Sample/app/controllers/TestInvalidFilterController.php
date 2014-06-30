<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Filter;

class TestInvalidFilterController extends CoreController
{
    /**
     * @Inject
     * @Filter("test", "test")
     */
    public function invalid()
    {
    }

    public function index()
    {
    }
}
