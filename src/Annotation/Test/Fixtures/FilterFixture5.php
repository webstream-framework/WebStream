<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Filter;

/**
 * before/afterフィルタでonlyが複数実行されること
 */
class FilterFixture5 implements IAnnotatable
{
    /**
     * @Filter(type="before", only={"beforeOnlyEnable", "beforeOnlyEnable2"})
     */
    public function before()
    {
        echo "b";
    }

    /**
     * @Filter(type="after", only={"afterOnlyEnable", "afterOnlyEnable2"})
     */
    public function after()
    {
        echo "a";
    }

    public function beforeOnlyEnable()
    {
        echo "boe";
    }

    public function beforeOnlyEnable2()
    {
        echo "boe2";
    }

    public function afterOnlyEnable()
    {
        echo "aoe";
    }

    public function afterOnlyEnable2()
    {
        echo "aoe2";
    }
}
