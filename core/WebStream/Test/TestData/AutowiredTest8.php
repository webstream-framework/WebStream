<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Autowired;
use WebStream\Annotation\Value;

class AutowiredTest8
{
    /**
     * @Autowired
     * @Value("name2")
     */
    private $name = "default2";

    public function getName2()
    {
        return $this->name;
    }
}
