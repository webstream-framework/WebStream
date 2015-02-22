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
}
