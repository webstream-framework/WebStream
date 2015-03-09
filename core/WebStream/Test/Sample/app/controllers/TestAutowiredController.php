<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Autowired;
use WebStream\Annotation\Template;

class TestAutowiredController extends CoreController
{
    /**
     * @Inject
     * @Autowired(value="honoka");
     */
    private $name;

    public function index1()
    {
        echo $this->name;
    }

    public function index2()
    {
        $this->TestAutowired->service1();
    }

    public function index3()
    {
        $this->TestAutowired->model1();
    }

    /**
     * @Inject
     * @Template("index.tmpl")
     */
    public function index4()
    {
    }
}
