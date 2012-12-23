<?php
namespace WebStream;
class TestResponseCacheController extends CoreController {
    /**
     * @Inject
     * @Cache("60")
     */
    public function execute() {
        sleep(10);
        echo "cache!";
    }
}