<?php
namespace WebStream;
class TestValidateErrorHandlingController extends CoreController {
    
    public function validate1() {}
    
    /**
     * @Inject
     * @Error("Validate")
     */
    public function validateError($e, $params) {
        echo $params["error"]["rule"];
    }
}