<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Autowired;
use WebStream\Annotation\Type;
use WebStream\Annotation\Value;

class AutowiredTest6
{
    /**
     * @Autowired("test", "test")
     * @Type("\WebStream\Test\TestData\AutowiredTestTypeConstructor")
     */
    private $instance;
}
