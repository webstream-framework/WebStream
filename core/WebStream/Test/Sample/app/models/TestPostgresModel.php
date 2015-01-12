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
class TestPostgresModel extends CoreModel
{
    public function model1()
    {
        $sql = "select * from T_WebStream limit :limit offset :offset";
        $bind = ["limit" => 1, "offset" => 0];

        return $this->select($sql, $bind);
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample.xml")
     */
    public function model2($bind = [])
    {
        $bind = ["limit" => 1, "offset" => 0];

        return $this->getTestData2($bind);
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample.xml")
     */
    public function model3()
    {
        $this->beginTransaction();
        $bind = ["name" => "kotori"];
        $this->setTestData($bind);
        $this->commit();

        $bind = ["limit" => 1, "offset" => 0];

        return $this->getTestData2($bind);
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample.xml")
     */
    public function model4()
    {
        $this->beginTransaction();
        $bind = ["name" => "kotori"];
        $this->setTestData($bind);
        $this->rollback();

        return $this->getTestDataNum();
    }

    /**
     * @Inject
     * @Query(file="query/dummy.xml")
     */
    public function model5()
    {
        $this->dummy();
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample.xml")
     */
    public function model6()
    {
        $bind = ["name" => "kotori"];
        $this->setTestData($bind);

        return $this->getTestDataNum();
    }

    public function model7()
    {
        // Modelメソッドを直接Controllerから呼ばないパターン
        return $this->model7_2();
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample-innercall.xml")
     */
    public function model7_2()
    {
        $bind = ["limit" => 1, "offset" => 0];

        return $this->innerSelectPostgres($bind);
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample-entity.xml")
     */
    public function model8()
    {
        return $this->entityMappingPostgres(["limit" => 1, "offset" => 0]);
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample-entity.xml")
     */
    public function model9()
    {
        return $this->entityMappingPostgres2(["limit" => 1, "offset" => 0]);
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample-entity.xml")
     */
    public function model10()
    {
        return $this->entityMappingMultipleTablePostgres();
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample-entity.xml")
     */
    public function model11()
    {
        return $this->entityMappingAliasPostgres();
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample.xml")
     */
    public function prepare()
    {
        $bind = ['name' => "kotori"];
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
        $this->beginTransaction();
        if ($this->deleteTestData() !== 0) {
            $this->commit();

            return true;
        } else {
            $this->rollback();

            return false;
        }
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
