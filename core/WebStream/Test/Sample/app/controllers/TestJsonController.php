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

    /**
     * @Inject
     * @Header(contentType="jsonp")
     */
    public function jsonp1()
    {
        echo "callback(" . json_encode(["name" => "<>'\""]) . ");";
    }

    /**
     * @Inject
     * @Header(contentType="jsonp")
     */
    public function jsonp2()
    {
        echo safetyOutJSONP(["name" => "<>'\""], "callback");
    }
}
