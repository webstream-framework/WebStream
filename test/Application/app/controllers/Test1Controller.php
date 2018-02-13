<?php
namespace WebStream\Test\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Attributes\Filter;
use WebStream\Annotation\Attributes\Header;
use WebStream\Annotation\Attributes\Template;
use WebStream\Annotation\Attributes\Custom\CustomControllerAnnotation;

class Test1Controller extends CoreController
{
    /**
     * @Filter(type="before")
     */
    public function init()
    {
        echo "<!DOCTYPE html>";
    }

    /**
     * @Header(allowMethod="get")
     * @Template("test1.tmpl")
     * @CustomControllerAnnotation
     */
    public function test1()
    {
        $this->Test1->test1();
    }
}
