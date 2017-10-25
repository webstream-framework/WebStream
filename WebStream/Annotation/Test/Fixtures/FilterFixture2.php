<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Filter;

/**
 * before/afterフィルタでexceptが実行されること
 */
class FilterFixture2 implements IAnnotatable
{
    /**
     * @Filter(type="before", except="beforeExceptEnable")
     */
    public function before()
    {
        echo "b";
    }

    /**
     * @Filter(type="after", except="afterExceptEnable")
     */
    public function after()
    {
        echo "a";
    }

    public function beforeExceptEnable()
    {
        echo "bee";
    }

    public function beforeExceptDisable()
    {
        echo "bed";
    }

    public function afterExceptEnable()
    {
        echo "aee";
    }

    public function afterExceptDisable()
    {
        echo "aed";
    }
}
