<?php
namespace WebStream\Sample;

use WebStream\Core\CoreModel;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Query;
use WebStream\Annotation\Database;

/**
 * @Inject
 * @Database(driver="WebStream\Database\Driver\Mysql", config="config/database.ini")
 */
class SampleModel extends CoreModel
{
    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample.xml")
     */
    public function getData()
    {
        return $this->getTestData(["limit" => 0, "offset" => 1]);
    }
}
