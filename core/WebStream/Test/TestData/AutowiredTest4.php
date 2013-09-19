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
    private $instance;

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

    public function getInstance()
    {
        return $this->instance;
    }

    public function getMail()
    {
        return $this->mail;
    }

    public function getAge()
    {
        return $this->age;
    }
}
