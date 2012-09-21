<?php
namespace WebStream;
class TestBasicAuthController extends CoreController {
    /**
     * @Inject
     * @BasicAuth("core/test/testdata/config/basic_auth.ini")
     */
    public function execute() {
        echo "basicauth";
    }
    
    /**
     * @Inject
     * @BasicAuth("dummy/basic_auth.ini")
     */
    public function execute2() {}
}