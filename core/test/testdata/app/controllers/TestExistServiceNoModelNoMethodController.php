<?php
namespace WebStream;
class TestExistServiceNoModelNoMethodController extends CoreController {
    public function execute() {
        $this->TestExistServiceNoModelNoMethod->get();
    }
}
