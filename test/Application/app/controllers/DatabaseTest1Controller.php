<?php
namespace WebStream\Test\Controller;

use WebStream\Core\CoreController;

class DatabaseTest1Controller extends CoreController
{
    public function mysql()
    {
        $data = $this->DatabaseTest1->getData();
        echo $data->toArray()[0]['name'];
    }
}
