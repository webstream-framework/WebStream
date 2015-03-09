<?php
namespace WebStream\Test\TestData\Sample\App\Service;

use WebStream\Core\CoreService;
use WebStream\Annotation\Inject;
use WebStream\Test\TestData\Sample\App\Annotation\CustomAnnotation3;

class TestCustomMethodsAnnotationService extends CoreService
{
    public function service1()
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
