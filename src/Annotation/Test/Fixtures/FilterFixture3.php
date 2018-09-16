<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Filter;

/**
 * before/afterフィルタでonlyが実行されること
 */
class FilterFixture3 implements IAnnotatable
{
    /**
     * @Filter(type="before", only="beforeOnlyEnable")
     */
    public function before()
    {
        echo "b";
    }

    /**
     * @Filter(type="after", only="afterOnlyEnable")
     */
    public function after()
    {
        echo "a";
    }

    public function beforeOnlyEnable()
    {
        echo "boe";
    }

    public function beforeOnlyDisable()
    {
        echo "bod";
    }

    public function afterOnlyEnable()
    {
        echo "aoe";
    }

    public function afterOnlyDisable()
    {
        echo "aod";
    }
}
