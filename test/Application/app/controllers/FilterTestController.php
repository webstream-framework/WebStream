<?php
namespace WebStream\Test\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Attributes\Filter;
use WebStream\Annotation\Attributes\Custom\CustomControllerAnnotation;

class FilterTestController extends CoreController
{
    /**
     * @Filter(type="before")
     */
    public function before()
    {
        echo "b";
    }

    /**
     * @Filter(type="after")
     */
    public function after()
    {
        echo "a";
    }

    /**
     * @CustomControllerAnnotation(name="custom")
     */
    public function action()
    {
        echo $this->annotation[CustomControllerAnnotation::class][0]['name'];
        echo "action";
    }
}
