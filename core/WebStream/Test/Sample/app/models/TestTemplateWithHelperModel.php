<?php
namespace WebStream\Test\TestData\Sample\App\Model;

use WebStream\Core\CoreModel;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Database;

/**
 * @Inject
 * @Database(driver="WebStream\Database\Driver\Mysql", config="config/database.mysql.ini")
 */
class TestTemplateWithHelperModel extends CoreModel
{
    public function model1()
    {
        $this->beginTransaction();
        $sql = "insert into T_WebStream (name) values (:name)";
        $bind = ["name" => "nicomaki"];
        $this->insert($sql, $bind);
        $this->commit();
        $sql = "select name from T_WebStream order by id desc limit 0, 1";

        return $this->select($sql);
    }
}
