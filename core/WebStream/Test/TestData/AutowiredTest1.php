<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Autowired;
use WebStream\Annotation\Type;
use WebStream\Annotation\Value;

class AutowiredTest1
{
    /**
     * @Autowired
     * @Type("\WebStream\Test\TestData\AutowiredTestType")
     */
    private $name;

    /**
     * @Autowired
     * @Value("kotori@lovelive.com")
     */
    private $mail;

    /**
     * @Autowired
     * @Value(17)
     */
    private $age;
}
