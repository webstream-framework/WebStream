<?php
namespace WebStream;
class TestFilterAnnotationController extends CoreController {
    /**
     * @Inject
     * @Filter("Before")
     */
    public function beforeFilter() {
        echo "before";
    }
    
    /**
     * @Inject
     * @Filter("After")
     */
    public function afterFilter() {
        echo "after";
    }
    
    public function execute() {
        echo "action";
    }
}
    