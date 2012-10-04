<?php
namespace WebStream;
class TestCacheAnnotationController extends CoreController {
    /**
     * @Inject
     * @Cache("10")
     */
    public function execute() {
        echo "test1";
    }
}
