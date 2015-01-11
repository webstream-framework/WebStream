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
class TestIteratorModel extends CoreModel
{
    public function model1($num = 1)
    {
        $sql = "select * from T_WebStream limit :limit, :offset";
        $bind = ["limit" => 0, "offset" => $num];

        return $this->select($sql, $bind);
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample.xml")
     */
    public function add($name)
    {
        $bind = ['name' => $name];
        $this->beginTransaction();
        if ($this->setTestData($bind) !== 0) {
            $this->commit();

            return true;
        } else {
            $this->rollback();

            return false;
        }
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample.xml")
     */
    public function clear()
    {
        $this->beginTransaction();
        if ($this->deleteTestData() !== 0) {
            $this->commit();

            return true;
        } else {
            $this->rollback();

            return false;
        }
    }
}
