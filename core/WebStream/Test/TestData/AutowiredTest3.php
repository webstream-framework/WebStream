<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Autowired;
use WebStream\Annotation\Value;

class AutowiredTest3
{
    /**
     * @Autowired
     * @Value(AutowiredConstant::HONOKA)
     */
    private $name;

    /**
     * @Autowired
     * @Value(AutowiredConstant::MEMBER_NUM)
     */
    private $memberNum;

    public function getName()
    {
        return $this->name;
    }

    public function getMemberNum()
    {
        return $this->memberNum;
    }
}
