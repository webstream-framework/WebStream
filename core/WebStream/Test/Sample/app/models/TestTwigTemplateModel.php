<?php
namespace WebStream\Test\TestData\Sample\App\Model;

use WebStream\Core\CoreModel;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Database;

/**
 * @Inject
 * @Database(driver="WebStream\Database\Driver\Mysql", config="config/database.mysql.ini")
 */
class TestTwigTemplateModel extends CoreModel
{
    public function model1()
    {
        $sql = "select * from T_WebStream limit :limit, :offset";
        $bind = ["limit" => 0, "offset" => 1];

        return $this->select($sql, $bind);
    }
}
