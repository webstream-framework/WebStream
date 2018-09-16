<?php
namespace WebStream\Annotation\Test\Fixtures;

use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Attributes\Query;

class QueryFixture1 implements IAnnotatable
{
    /**
     * @Query(file="database-mapper.xml")
     */
    public function action1()
    {
    }
}
