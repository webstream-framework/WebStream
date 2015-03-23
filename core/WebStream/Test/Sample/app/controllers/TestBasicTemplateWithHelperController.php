<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Template;

class TestBasicTemplateWithHelperController extends CoreController
{
    /**
     * @Inject
     * @Template("index1.tmpl")
     */
    public function index1()
    {
        // helperオブジェクトが取得できること
    }
}