<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Test\TestData\Sample\App\Annotation\CustomAnnotation3;

class TestCustomMethodsAnnotationController extends CoreController
{
    public function index1()
    {
        echo "ni-";
    }

    /**
     * @Inject
     * @CustomAnnotation3
     */
    public function read1()
    {
    }

    /**
     * @Inject
     * @CustomAnnotation3
     */
    public function read2()
    {
    }
}
