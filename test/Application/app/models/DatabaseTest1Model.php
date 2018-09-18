<?php
namespace WebStream\Test\Model;

use WebStream\Core\CoreModel;
use WebStream\Annotation\Attributes\Database;

/**
 * @Database(driver="WebStream\Database\Driver\Mysql", config="config/database.mysql.ini")
 */
class DatabaseTest1Model extends CoreModel
{
    public function getData()
    {
        return $this->select("SELECT * FROM T_WebStream LIMIT :limit, :offset", ["limit" => 0, "offset" => 1])
            ->toEntity("WebStream\Test\Model\Entity\TestEntitiy");
    }
}
