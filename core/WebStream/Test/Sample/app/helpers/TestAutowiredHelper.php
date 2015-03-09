<?php
namespace WebStream\Test\TestData\Sample\App\Helper;

use WebStream\Core\CoreHelper;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Autowired;

class TestAutowiredHelper extends CoreHelper
{
    /**
     * @Inject
     * @Autowired(value="kotohonoumi");
     */
    private $name;

    public function helper1()
    {
        echo $this->name;
    }
}
