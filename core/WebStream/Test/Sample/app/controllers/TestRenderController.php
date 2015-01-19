<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Autowired;
use WebStream\Annotation\Template;
use WebStream\Annotation\Filter;
use WebStream\Annotation\Value;
use WebStream\Annotation\Type;

class TestRenderController extends CoreController
{
    /**
     * @Autowired
     * @Value("みかしー")
     */
    private $name;

    /**
     * @Inject
     * @Filter("Before")
     */
    public function before()
    {
        var_dump($this->name);
    }

    /**
     * @Inject
     * @Template("base.tmpl")
     * @Template("test.tmpl", name="embed", type="parts")
     * @Template("mikashi.tmpl", name="mikashi_owata", type="parts")
     */
    public function index()
    {
     // * @Template("head.tmpl", name="head", type="shared")
     // * @Template("head_ad.tmpl", name="head_ad", type="shared")
     // * @Template("base.tmpl")
     // * @Template("test.tmpl", name="embed")
     // * @Template("mikashi.tmpl", name="mikashi_owata")

        return ["name" => "(・8・)"];
    }

    /**
     * @Inject
     * @Filter("After")
     */
    public function after()
    {
        var_dump("after");
    }
}
