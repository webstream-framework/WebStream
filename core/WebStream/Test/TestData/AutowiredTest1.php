<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Autowired;
use WebStream\Annotation\Inject;

class AutowiredTest1
{
    /**
     * @Inject
     * @Autowired(type="\WebStream\Test\TestData\AutowiredTestType")
     */
    private $instance;

    /**
     * @Inject
     * @Autowired(value="kotori@lovelive.com")
     */
    private $mail;

    /**
     * @Inject
     * @Autowired(value=17)
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
