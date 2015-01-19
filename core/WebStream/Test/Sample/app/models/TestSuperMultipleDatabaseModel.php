<?php
namespace WebStream\Test\TestData\Sample\App\Model;

use WebStream\Core\CoreModel;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Query;
use WebStream\Annotation\Database;

/**
 * @Inject
 * @Database(driver="WebStream\Database\Driver\Postgresql", config="config/database.postgresql.ini")
 */
class TestSuperMultipleDatabaseModel extends CoreModel
{
    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample-multipledb.xml")
     */
    public function model1()
    {
        $bind = ["limit" => 1, "offset" => 0];

        return $this->usePostgres($bind);
    }
}
