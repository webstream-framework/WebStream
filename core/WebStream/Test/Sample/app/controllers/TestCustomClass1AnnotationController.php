<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Test\TestData\Sample\App\Annotation\CustomAnnotation4;

/**
 * @Inject
 * @CustomAnnotation4
 */
class TestCustomClass1AnnotationController extends CoreController
{
    public function index1()
    {
    }

    public function index2()
    {
        $this->TestCustomClass1Annotation->service1();
    }

    public function index3()
    {
        $this->TestCustomClass1Annotation->model1();
    }
}
