<?php
namespace WebStream\Test\Controller;

use WebStream\Core\CoreController;

class DatabaseTest2Controller extends CoreController
{
    public function postgresql()
    {
        $data = $this->DatabaseTest2->getData();
        var_dump($data->toArray());
    }
}
