<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Template;

class TestHelperController extends CoreController
{
    /**
     * @Inject
     * @Template("base1.tmpl")
     */
    public function help1()
    {
    }

    /**
     * @Inject
     * @Template("base2.tmpl")
     */
    public function help2()
    {
        return ["name" => "μ's"];
    }

    /**
     * @Inject
     * @Template("base3.tmpl")
     */
    public function help3()
    {
        return ["name" => "LilyWhite"];
    }

    /**
     * @Inject
     * @Template("base4.tmpl")
     */
    public function help4()
    {
        return ["name" => "BiBi"];
    }

    /**
     * @Inject
     * @Template("base5.tmpl")
     */
    public function help5()
    {
    }

    /**
     * @Inject
     * @Template("base6.tmpl")
     */
    public function help6()
    {
        return [
            "name" => "honoka",
            "age" => 16
        ];
    }
}
