<?php
namespace WebStream\Test\TestData;

use WebStream\Annotation\Autowired;
use WebStream\Annotation\Inject;

class AutowiredTest3
{
    /**
     * @Inject
     * @Autowired(value=AutowiredConstant::HONOKA)
     */
    private $name;

    /**
     * @Inject
     * @Autowired(value=AutowiredConstant::MEMBER_NUM)
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
