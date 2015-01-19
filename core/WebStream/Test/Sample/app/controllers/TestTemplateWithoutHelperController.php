<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Template;

class TestTemplateWithoutHelperController extends CoreController
{
    /**
     * @Inject
     * @Template("base1.tmpl")
     */
    public function index1()
    {
    }
}
