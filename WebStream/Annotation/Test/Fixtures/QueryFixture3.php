<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Query;

class QueryFixture3 implements IAnnotatable
{
    /**
     * @Query(file="undefined.xml")
     */
    public function action1()
    {
    }
}
