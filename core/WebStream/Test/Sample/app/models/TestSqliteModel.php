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
        return $this->model5();
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample-innercall.xml")
     */
    public function model5()
    {
        $bind = ["limit" => 1, "offset" => 0];

        return $this->innerSelectSqlite($bind);
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample.xml")
     */
    public function prepare()
    {
        $bind = ['name' => "honoka"];

        return $this->deleteTestData() !== 0 && $this->setTestData($bind) !== 0;
    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample.xml")
     */
    public function clear()
    {
        return $this->deleteTestData() !== 0;
    }
}
