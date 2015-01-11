<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Autowired;
use WebStream\Annotation\Inject;

class AutowiredTest6
{
    /**
     * @Inject
     * @Autowired(hoge="\WebStream\Test\TestData\AutowiredTestTypeConstructor")
     */
    private $instance;

    public function getInstance()
    {
        return $this->instance;
    }
}
