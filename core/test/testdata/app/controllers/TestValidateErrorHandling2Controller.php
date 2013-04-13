<?php
namespace WebStream;
class TestValidateErrorHandling2Controller extends CoreController {
    
    public function validate1() {}
    
    /**
     * @Inject
     * @Error("Validate")
     */
    public function validateError($e, $params) {
        echo $params["error"]["rule"];
    }
}