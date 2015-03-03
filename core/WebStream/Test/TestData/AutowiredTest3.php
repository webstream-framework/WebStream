<?php
namespace WebStream\Test\TestData;

use WebStream\Core\CoreInterface;
use WebStream\Annotation\Autowired;
use WebStream\Annotation\Inject;
use WebStream\Module\Container;

class AutowiredTest3 implements CoreInterface
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

    public function __construct(Container $container) {}

    public function __destruct() {}

    public function __initialize(Container $container) {}

    public function getName()
    {
        return $this->name;
    }

    public function getMemberNum()
    {
        return $this->memberNum;
    }
}
