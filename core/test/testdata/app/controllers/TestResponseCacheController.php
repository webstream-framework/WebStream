<?php
namespace WebStream;
class TestResponseCacheController extends CoreController {
    /**
     * @Inject
     * @Cache("10")
     */
    public function execute() {
        sleep(10);
        echo "cache!";
    }
}