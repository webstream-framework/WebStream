<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Filter;

/**
 * skipフィルタが実行されること
 */
class FilterFixture6 implements IAnnotatable
{
    /**
     * @Filter(type="before")
     */
    public function before()
    {
        echo "b1";
    }

    /**
     * @Filter(type="after")
     */
    public function after()
    {
        echo "b2";
    }

    /**
     * @Filter(type="skip", except="before")
     */
    public function skipEnable()
    {
        echo "se";
    }

    /**
     * @Filter(type="skip", except={"before", "after"})
     */
    public function multipleSkipEnable()
    {
        echo "mse";
    }
}
