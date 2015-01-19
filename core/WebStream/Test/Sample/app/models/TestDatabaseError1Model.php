<?php
namespace WebStream\Test\TestData\Sample\App\Model;

use WebStream\Core\CoreModel;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Query;
use WebStream\Annotation\Database;

/**
 * @Inject
 * @Database(driver="WebStream\Database\Driver\UndefinedDriver", config="config/database.mysql.ini")
 */
class TestDatabaseError1Model extends CoreModel
{
    public function model1()
    {
    }
}
