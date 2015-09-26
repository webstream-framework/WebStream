<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Template;

class TestViewModelInModelController extends CoreController
{
    /**
     * @Inject
     * @Template("model1.tmpl")
     */
    public function model1()
    {
        $this->TestViewModelInModel->model1();
    }

    /**
     * @Inject
     * @Template("model2.tmpl")
     */
    public function model2()
    {
        $this->TestViewModelInModel->model2();
    }
}
