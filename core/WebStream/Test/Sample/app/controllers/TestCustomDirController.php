<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Template;

class TestCustomDirController extends CoreController
{
    public function fromController()
    {
        echo get_class(new \WebStream\Test\TestData\Sample\App\Entity\TestEntity());
    }

    public function fromService()
    {
        $obj = $this->TestCustomDir->service1();
        echo get_class($obj);
    }

    public function fromModel()
    {
        $obj = $this->TestCustomDir->model1();
        echo get_class($obj);
    }

    /**
     * @Inject
     * @Template("index.tmpl")
     */
    public function fromView()
    {
    }

    /**
     * @Inject
     * @Template("helper.tmpl")
     */
    public function fromHelper()
    {
    }
}
