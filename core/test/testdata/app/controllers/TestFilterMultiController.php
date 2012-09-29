<?php
namespace WebStream;
class TestFilterMultiController extends AppController {
    /**
     * @Inject
     * @Filter("Before")
     */
    public function testChild() {
        echo "child before";
    }
    
    public function execute() {
        echo "execute";
    }

    /**
     * @Inject
     * @Filter("After")
     */
    public function testChild2() {
        echo "child after";
    }
}