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
class TestMysqlModel extends CoreModel
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
    public function model2($bind = [])
    {
        $bind = ["limit" => 0, "offset" => 1];

        return $this->getTestData($bind);
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

        $bind = ["limit" => 0, "offset" => 1];

        return $this->getTestData($bind);
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
        return $this->model8();
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample-innercall.xml")
     */
    public function model8()
    {
        $bind = ["limit" => 0, "offset" => 1];

        return $this->innerSelectMysql($bind);
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample.xml")
     */
    public function prepare()
    {
        $bind = ['name' => "honoka"];
        $this->beginTransaction();
        if ($this->deleteTestData() !== 0 && $this->setTestData($bind) !== 0) {
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
