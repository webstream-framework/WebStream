<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Test\TestData\Sample\App\Annotation\CustomAnnotation7;

class TestCustomProperty2AnnotationController extends CoreController
{
    /**
     * @Inject
     * @CustomAnnotation7
     */
    private $name1;

    /**
     * @Inject
     * @CustomAnnotation7
     */
    private $name2;

    public function index1()
    {
        echo "e";
    }
}
