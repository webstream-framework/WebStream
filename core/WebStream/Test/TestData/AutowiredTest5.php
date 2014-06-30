<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Autowired;
use WebStream\Annotation\Type;
use WebStream\Annotation\Value;

class AutowiredTest5
{
    /**
     * @Type("\WebStream\Test\TestData\AutowiredTestTypeConstructor")
     * @Autowired
     */
    private $instance;

    public function getInstance()
    {
        return $this->instance;
    }
}
