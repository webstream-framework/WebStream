<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Autowired;
use WebStream\Annotation\Inject;

class AutowiredTest7 extends AutowiredTest8
{
    /**
     * @Inject
     * @Autowired(value="name1")
     */
    private $name = "default1";

    /**
     * @Inject
     * @Autowired(value="name2")
     */
    private $name2;

    public function getName()
    {
        return $this->name;
    }

    public function getName2()
    {
        return $this->name2;
    }

    public function getName3()
    {
        return parent::getName3();
    }

    public function getName4()
    {
        return parent::getName4();
    }
}
