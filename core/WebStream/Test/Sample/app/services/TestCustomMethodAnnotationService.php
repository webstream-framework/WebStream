<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreService;
use WebStream\Annotation\Inject;
use WebStream\Annotation\ExceptionHandler;
use WebStream\Test\TestData\Sample\App\Annotation\CustomAnnotation1;

class TestCustomMethodAnnotationService extends CoreService
{
    /**
     * @Inject
     * @CustomAnnotation1(exception=false)
     */
    public function service1()
    {
        echo "niconiconi-";
    }

    /**
     * @Inject
     * @CustomAnnotation1(exception=true)
     */
    public function service2()
    {
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
