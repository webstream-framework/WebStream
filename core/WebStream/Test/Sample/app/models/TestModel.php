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
class TestModel extends CoreModel
{
    /**
     */
    public function model1()
    {
        $sql = "select * from users limit :limit, :offset";
        $bind = ["limit" => 0, "offset" => 3];
        $result = $this->select($sql, $bind);

        // TODO
        // 配列アクセスを許可するが、配列アクセスした瞬間にメモリに全てのデータを
        // 確保する。通常はイテレーションしかしないようにする。

        //var_dump($result->count());

        var_dump($result[0]);
        // var_dump(isset($result[0]["user_id"]));

        // $result->toArray();

        //
        // foreach ($result as $key => $value) {
        //     var_dump($key);
        //     var_dump($value);
        // }

        //$result->seek(100);
        // $result[0] = "test";

        // var_dump($result[0]);
        // var_dump(empty($result[10]));

        //

    }

    /**
     * @Inject
     * @Query(file="query/webstream-model-mapper-sample.xml")
     */
    public function model2($bind = [])
    {
        $bind = ["id" => 5133];
        return $this->selectSample($bind);
    }
}
