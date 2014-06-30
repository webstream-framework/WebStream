<?php
namespace WebStream\Test\TestData\Sample\App\Model;

use WebStream\Core\CoreModel;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Query;
use WebStream\Annotation\Database;

/**
 * @Inject
 * @Database(driver="WebStream\Database\Driver\Mysql", config="config/database.mysql.ini")
 */
class TestDatabaseError3Model extends CoreModel
{
    /**
     * @Inject
     * @Query(file="query/webstream-invalid-model-mapper-sample.xml")
     */
    public function model1()
    {
        $bind = ["limit" => 0, "offset" => 1];

        return $this->getTestData($bind);
    }
}
