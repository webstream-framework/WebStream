<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;

class TestTransactionController extends CoreController
{
    public function transactionInController()
    {
        $this->TestTransaction->beginTransaction();
        $this->TestTransaction->model1();
        $this->TestTransaction->model2();
        $this->TestTransaction->commit();

        $result = $this->TestTransaction->model3();

        foreach ($result as $value) {
            echo $value["name"];
        }
    }

    public function transactionRollbackInController()
    {
        $this->TestTransaction->beginTransaction();
        $this->TestTransaction->model1();
        $this->TestTransaction->model2();
        $this->TestTransaction->rollback();

        $result = $this->TestTransaction->model3();

        foreach ($result as $value) {
            echo $value["name"];
        }
    }

    public function transactionInModel()
    {
        $result = $this->TestTransaction->model4();

        foreach ($result as $value) {
            echo $value["name"];
        }
    }

    public function clear()
    {
        $this->TestTransaction->clear();
    }
}
