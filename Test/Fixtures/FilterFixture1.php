<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Filter;

/**
 * before/afterフィルタが実行されること
 */
class FilterFixture1 implements IAnnotatable
{
    /**
     * @Filter(type="before")
     */
    public function before1()
    {
        echo "b1";
    }

    /**
     * @Filter(type="before")
     */
    public function before2()
    {
        echo "b2";
    }

    /**
     * @Filter(type="after")
     */
    public function after1()
    {
        echo "a1";
    }

    /**
     * @Filter(type="after")
     */
    public function after2()
    {
        echo "a2";
    }

    public function action()
    {
    }
}
