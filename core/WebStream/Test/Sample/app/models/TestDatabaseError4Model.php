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
class TestDatabaseError4Model extends CoreModel
{
    /**
     * @Inject
     * @Query(file="query/webstream-invalid-file-sample.xml")
     */
    public function model1()
    {
        $this->invalid();
    }
}
