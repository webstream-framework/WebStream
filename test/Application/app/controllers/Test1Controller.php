<?php
namespace WebStream\Test\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Filter;
use WebStream\Annotation\Attributes\Header;
use WebStream\Annotation\Attributes\Template;

class Test1Controller extends CoreController
{
    /**
     * @Filter(type="before")
     */
    public function init()
    {
        echo "init";
    }

    /**
     * @Header(allowMethod="get")
     * @Template("test1.tmpl")
     */
    public function test1()
    {
        echo "controller";
    }
}
