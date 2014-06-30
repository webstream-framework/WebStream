<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Autowired;
use WebStream\Annotation\Inject;

class AutowiredTest2
{
    /**
     * @Inject
     * @Autowired(type="\Dummy\Test")
     */
    private $dummy;
}
