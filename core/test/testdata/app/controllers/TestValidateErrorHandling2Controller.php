<?php
namespace WebStream;
class TestValidateErrorHandling2Controller extends CoreController {
    
    public function validate1() {}
    
    /**
     * @Inject
     * @Error("Validate")
     */
    public function validate_error($params) {
        echo $params["error"]["rule"];
    }
}