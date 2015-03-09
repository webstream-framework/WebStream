<?php
namespace WebStream\Test\TestData\Sample\App\Model;

use WebStream\Core\CoreModel;
use WebStream\Annotation\Inject;
use WebStream\Test\TestData\Sample\App\Annotation\CustomAnnotation5;

/**
 * @Inject
 * @CustomAnnotation5
 */
class TestCustomClass2AnnotationModel extends CoreModel
{
    public function model1()
    {
        echo $this->annotation["WebStream\Test\TestData\Sample\App\Annotation\CustomAnnotation5"][0];
        echo "model";
    }
}
