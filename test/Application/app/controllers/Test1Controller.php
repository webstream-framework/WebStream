<?php
namespace WebStream\Test\Controller;

use WebStream\Core\CoreController;
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
        $ttt = $this->Test1->test1();
        var_dump($ttt);
    }
}
