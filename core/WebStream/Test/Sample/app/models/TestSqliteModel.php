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
     * @Inject
     * @Query(file="query/dummy.xml")
     */
    public function model3()
    {
        $this->dummy();
    }

    public function model4()
    {
        // Modelメソッドを直接Controllerから呼ばないパターン
        return $this->model4_2();
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample-innercall.xml")
     */
    public function model4_2()
    {
        $bind = ["limit" => 1, "offset" => 0];

        return $this->innerSelectSqlite($bind);
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample-entity.xml")
     */
    public function model5()
    {
        return $this->entityMappingSqlite(["limit" => 1, "offset" => 0]);
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample-entity.xml")
     */
    public function model6()
    {
        return $this->entityMappingSqlite2(["limit" => 1, "offset" => 0]);
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample.xml")
     */
    public function prepare()
    {
        $bind = ['name' => "honoka"];
        $this->deleteTestData();

        return $this->setTestData($bind) !== 0;
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample-entity.xml")
     */
    public function prepare2()
    {
        $bind = ['value1' => "honoka", 'value2' => "kotori", 'value3' => "umichang"];
        $this->beginTransaction();
        $this->deleteTestData();
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
        return $this->deleteTestData() !== 0;
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample-entity.xml")
     */
    public function clear2()
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
