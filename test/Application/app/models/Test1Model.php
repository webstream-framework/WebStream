<?php
namespace WebStream\Test\Model;

use WebStream\Core\CoreModel;
use WebStream\Annotation\Attributes\Database;
use WebStream\Annotation\Attributes\Query;

/**
 * @Database(driver="WebStream\Database\Driver\Mysql", config="config/database.mysql.ini")
 */
class Test1Model extends CoreModel
{
    /**
     * @Query(file="query/webstream-test-mysql-mapper.xml")
     * @Query(file="query/webstream-test-mysql-mapper.xml")
     */
    public function test1()
    {
        return $this->querySelect(["limit" => 0, "offset" => 1]);
    }
}
