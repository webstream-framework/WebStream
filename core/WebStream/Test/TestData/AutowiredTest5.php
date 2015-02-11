<?php
namespace WebStream\Test\TestData;

use WebStream\Core\CoreInterface;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Autowired;
use WebStream\Module\Container;

class AutowiredTest5 implements CoreInterface
{
    /**
     * @Inject
     * @Autowired(type="\WebStream\Test\TestData\AutowiredTestTypeConstructor")
     */
    private $instance;

    public function __construct(Container $container) {}

    public function __destruct() {}

    public function getInstance()
    {
        return $this->instance;
    }
}
