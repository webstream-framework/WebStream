<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\ExceptionHandler;
use WebStream\Test\TestData\Sample\App\Annotation\CustomAnnotation1;
use WebStream\Test\TestData\Sample\App\Annotation\CustomAnnotation2;

class TestCustomMethodAnnotationController extends CoreController
{
    /**
     * @Inject
     * @CustomAnnotation1(exception=false)
     */
    public function index1()
    {
        echo "niconiconi-";
    }

    /**
     * @Inject
     * @CustomAnnotation1(exception=true)
     */
    public function index2()
    {
    }

    /**
     * @Inject
     * @CustomAnnotation2
     */
    public function index3()
    {
        echo $this->annotation["WebStream\Test\TestData\Sample\App\Annotation\CustomAnnotation2"][0];
    }

    /**
     * @Inject
     * @ExceptionHandler("\Exception")
     */
    public function exceptionHandler($params)
    {
        echo "makimakima-";
    }
}
