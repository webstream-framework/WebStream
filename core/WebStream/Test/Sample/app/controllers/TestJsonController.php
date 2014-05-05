<?php
namespace WebStream\Test\TestData\Sample\App\Controller;

use WebStream\Core\CoreController;
use WebStream\Annotation\Inject;
use WebStream\Annotation\Header;

class TestJsonController extends CoreController
{
    /**
     * @Inject
     * @Header(contentType="json")
     */
    public function json1()
    {
        echo json_encode(["name" => "<>'\""]);
    }

    /**
     * @Inject
     * @Header(contentType="json")
     */
    public function json2()
    {
        echo safetyOutJSON(["name" => "<>'\""]);
    }
}
