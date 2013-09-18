<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Autowired;
use WebStream\Annotation\Type;
use WebStream\Annotation\Value;

class AutowiredTest4
{
    /**
     * @Type("\WebStream\Test\TestData\AutowiredTestType")
     * @Autowired
     */
    private $name;

    /**
     * @Value("kotori@lovelive.com")
     * @Autowired
     */
    private $mail;

    /**
     * @Value(17)
     * @Autowired
     */
    private $age;
}
