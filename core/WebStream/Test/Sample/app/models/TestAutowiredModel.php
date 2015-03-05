<?php
namespace WebStream\Test\TestData\Sample\App\Model;

use WebStream\Core\CoreModel;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Autowired;

class TestAutowiredModel extends CoreModel
{
    /**
     * @Inject
     * @Autowired(value="umichang");
     */
    private $name;

    public function model1()
    {
        echo $this->name;
    }
}
