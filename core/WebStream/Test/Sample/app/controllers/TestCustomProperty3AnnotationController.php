<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Test\TestData\Sample\App\Annotation\CustomAnnotation8;

class TestCustomProperty3AnnotationController extends CoreController
{
    /**
     * @Inject
     * @CustomAnnotation8
     */
    private $name;

    public function index1()
    {
        echo $this->annotation["WebStream\Test\TestData\Sample\App\Annotation\CustomAnnotation8"][0];
    }
}
