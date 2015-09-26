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

    /**
     * @Inject
     * @Template("index2.tmpl")
     */
    public function index2()
    {
        // helperの中に記述したテンプレート記法を展開できること
    }

    /**
     * @Inject
     * @Template("index3.tmpl")
     */
    public function index3()
    {
        // helperの中に記述したテンプレート記法を3段階展開できること
    }
}
