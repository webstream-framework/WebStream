<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;

class TestSessionController extends CoreController
{
    public function setSessionLimitExpire()
    {
        $this->session->restart(3);
        echo "今から3秒間有効";
    }

    public function setSessionNoLimitExpire()
    {
        $this->session->restart(); // 引数なしの場合(null)、セッションスコープ
        echo "セッションスコープで有効";
    }

    public function index1()
    {
        echo "セッション有効中";
    }
}
