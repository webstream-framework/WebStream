<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Autowired;
use WebStream\Annotation\Value;

class AutowiredTest7 extends AutowiredTest8
{
    /**
     * @Autowired
     * @Value("name1")
     */
    private $name = "default1";

    public function getName()
    {
        return $this->name;
    }

    public function getName2()
    {
        return parent::getName2();
    }
}
