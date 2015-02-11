<?php
namespace WebStream\Test\TestData;

use WebStream\Core\CoreInterface;
use WebStream\Annotation\Autowired;
use WebStream\Annotation\Inject;
use WebStream\Module\Container;

class AutowiredTest2 implements CoreInterface
{
    /**
     * @Inject
     * @Autowired(type="\Dummy\Test")
     */
    private $dummy;

    public function __construct(Container $container) {}

    public function __destruct() {}
}
