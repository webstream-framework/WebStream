<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Filter;

/**
 * before/afterフィルタでexceptが複数実行されること
 */
class FilterFixture4 implements IAnnotatable
{
    /**
     * @Filter(type="before", except={"beforeExceptEnable", "beforeExceptEnable2"})
     */
    public function before()
    {
        echo "b";
    }

    /**
     * @Filter(type="after", except={"afterExceptEnable", "afterExceptEnable2"})
     */
    public function after()
    {
        echo "a";
    }

    public function beforeExceptEnable()
    {
        echo "bee";
    }

    public function beforeExceptEnable2()
    {
        echo "bee2";
    }

    public function afterExceptEnable()
    {
        echo "aee";
    }

    public function afterExceptEnable2()
    {
        echo "aee2";
    }
}
