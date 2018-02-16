<?php
namespace WebStream\Test\Helper;

use WebStream\Core\CoreHelper;

class Test1Helper extends CoreHelper
{
    public function write($list)
    {
        foreach ($list as $elem) {
            echo $this->encodeHtml($elem->getName());
        }
    }
}
