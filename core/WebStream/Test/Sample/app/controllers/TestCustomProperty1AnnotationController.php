<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Test\TestData\Sample\App\Annotation\CustomAnnotation6;

class TestCustomProperty1AnnotationController extends CoreController
{
    /**
     * @Inject
     * @CustomAnnotation6
     */
    private $name;

    public function index1()
    {
    }
}
