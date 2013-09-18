<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Autowired;
use WebStream\Annotation\Type;
use WebStream\Annotation\Value;

class AutowiredTest2
{
    /**
     * @Autowired
     * @Type("\Dummy\Test")
     */
    private $dummy;
}
