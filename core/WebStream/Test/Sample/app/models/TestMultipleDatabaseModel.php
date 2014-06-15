<?php
namespace WebStream\Test\TestData\Sample\App\Model;

use WebStream\Annotation\Inject;
use WebStream\Annotation\Query;
use WebStream\Annotation\Database;

/**
 * @Inject
 * @Database(driver="WebStream\Database\Driver\Mysql", config="config/database.mysql.ini")
 */
class TestMultipleDatabaseModel extends TestSuperMultipleDatabaseModel
{
    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample-multipledb.xml")
     */
    public function model1()
    {
        $bind = ["limit" => 0, "offset" => 1];
        $result1 = $this->useMysql($bind);

        foreach ($result1 as $value) {
            echo $value["name"];
        }

        $result2 = parent::model1();

        foreach ($result2 as $value) {
            echo $value["name"];
        }
    }
}
