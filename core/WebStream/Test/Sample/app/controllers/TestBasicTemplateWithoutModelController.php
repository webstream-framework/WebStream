<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Template;

class TestBasicTemplateWithoutModelController extends CoreController
{
    /**
     * @Inject
     * @Template("index1.tmpl")
     */
    public function index1()
    {
        // modelオブジェクトがCoreExceptionDelegator
    }

    /**
     * @Inject
     * @Template("error1.tmpl")
     */
    public function error1()
    {
        // modelなし
    }
}
