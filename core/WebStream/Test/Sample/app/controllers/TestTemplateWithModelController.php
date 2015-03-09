<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Template;

class TestTemplateWithModelController extends CoreController
{
    /**
     * @Inject
     * @Template("base1.tmpl")
     */
    public function index1()
    {
    }

    /**
     * @Inject
     * @Template("base2.tmpl")
     */
    public function index2()
    {
    }

    /**
     * @Inject
     * @Template("base3.tmpl")
     */
    public function index3()
    {
    }
}
