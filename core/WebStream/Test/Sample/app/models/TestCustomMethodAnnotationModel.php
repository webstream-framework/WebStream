<?php
namespace WebStream\Test\TestData\Sample\App\Model;

use WebStream\Core\CoreModel;
use WebStream\Annotation\Inject;
use WebStream\Annotation\ExceptionHandler;
use WebStream\Test\TestData\Sample\App\Annotation\CustomAnnotation1;
use WebStream\Test\TestData\Sample\App\Annotation\CustomAnnotation2;

class TestCustomMethodAnnotationModel extends CoreModel
{
    /**
     * @Inject
     * @CustomAnnotation1(exception=false)
     */
    public function model1()
    {
        echo "niconiconi-";
    }

    /**
     * @Inject
     * @CustomAnnotation1(exception=true)
     */
    public function model2()
    {
    }

    /**
     * @Inject
     * @CustomAnnotation2
     */
    public function model3()
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
