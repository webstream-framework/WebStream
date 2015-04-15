<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Template;

class TestBasicTemplateWithoutHelperController extends CoreController
{
    /**
     * @Inject
     * @Template("index1.tmpl")
     */
    public function index1()
    {
        // helperオブジェクトがCoreExceptionDelegator
    }

    /**
     * @Inject
     * @Template("error1.tmpl")
     */
    public function error1()
    {
        // helperなし
    }
}
