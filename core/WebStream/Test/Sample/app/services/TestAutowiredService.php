<?php
namespace WebStream\Test\TestData\Sample\App\Service;

use WebStream\Core\CoreService;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Autowired;

class TestAutowiredService extends CoreService
{
    /**
     * @Inject
     * @Autowired(value="kotori");
     */
    private $name;

    public function service1()
    {
        echo $this->name;
    }

    public function service2()
    {
        $this->TestAutowired->model1();
    }
}
