<?php
namespace WebStream\Test\Model;

use WebStream\Core\CoreModel;
use WebStream\Annotation\Attributes\Database;

/**
 * @Database(driver="WebStream\Database\Driver\Postgresql", config="config/database.postgresql.ini")
 */
class DatabaseTest2Model extends CoreModel
{
    public function getData()
    {
        return $this->select("SELECT * FROM T_WebStream LIMIT :limit OFFSET :offset", ["limit" => 1, "offset" => 0])
            ->toEntity("WebStream\Test\Model\Entity\TestEntitiy");
    }
}
