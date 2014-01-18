<?php
namespace WebStream\Test\TestData\Sample\App\Model;

use WebStream\Core\CoreModel;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Query;
use WebStream\Annotation\Database;

/**
 * @Inject
 * @Database(driver="WebStream\Database\Driver\Sqlite", config="config/database.sqlite.ini")
 */
class TestSqliteModel extends CoreModel
{
    public function model1()
    {
        $sql = "select * from T_WebStream limit :limit, :offset";
        $bind = ["limit" => 0, "offset" => 1];
        return $this->select($sql, $bind);
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample.xml")
     */
    public function model2()
    {
        $bind = ["limit" => 0, "offset" => 1];
        return $this->getTestData($bind);
    }

    /**
     */
    public function model3($bind = [])
    {
        // $this->beginTransaction();
        $sql = "insert into T_WebStream (name) values (:name)";
        $bind = ["name" => "(・8・)"];
        $count = $this->insert($sql, $bind);
        var_dump($count);
        // $this->commit();
    }
}
