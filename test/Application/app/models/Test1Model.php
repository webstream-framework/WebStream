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
    public function test1()
    {
        $sql = "SELECT * FROM T_WebStream LIMIT :limit, :offset";
        $bind = ["limit" => 0, "offset" => 1];

        return $this->select($sql, $bind);
    }
}
