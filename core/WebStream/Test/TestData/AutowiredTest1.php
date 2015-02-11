<?php
namespace WebStream\Test\TestData;

use WebStream\Core\CoreInterface;
use WebStream\Annotation\Autowired;
use WebStream\Annotation\Inject;
use WebStream\Module\Container;

class AutowiredTest1 implements CoreInterface
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

    public function __construct(Container $container) {}

    public function __destruct() {}

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
