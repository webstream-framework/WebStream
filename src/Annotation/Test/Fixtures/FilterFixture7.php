<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Filter;

/**
 * exceptとonlyを同時に指定した場合、exceptが有効になること
 * アクションメソッド「だけ=only」でフィルタが有効になり、アクションメソッドで「除外=except」するのでexceptが有効
 */
class FilterFixture7 implements IAnnotatable
{
    /**
     * @Filter(type="before", except="action", only="action")
     */
    public function before()
    {
        echo "b";
    }

    public function action()
    {
        echo "a";
    }
}
