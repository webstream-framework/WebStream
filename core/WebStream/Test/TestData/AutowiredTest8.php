<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Autowired;
use WebStream\Annotation\Inject;

class AutowiredTest8
{
    /**
     * @Inject
     * @Autowired(value="name3")
     */
    private $name3 = "default3";

    /**
     * @Inject
     * @Autowired(value="name4")
     */
    private $name4;

    public function getName3()
    {
        return $this->name3;
    }

    public function getName4()
    {
        return $this->name4;
    }
}
