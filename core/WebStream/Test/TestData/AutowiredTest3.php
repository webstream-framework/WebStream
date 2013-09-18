<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Autowired;
use WebStream\Annotation\Type;
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
}
