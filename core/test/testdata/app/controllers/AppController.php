<?php
namespace WebStream;
class AppController extends CoreController {
    /**
     * @Inject
     * @Filter("Before")
     */
    public function testParent() {
        echo "super before";
    }

    /**
     * @Inject
     * @Filter("After")
     */
    public function testParent2() {
        echo "super after";
    }
}