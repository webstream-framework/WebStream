<?php
namespace WebStream;
class TestNoServiceExistModelNoMethodController extends CoreController {
    public function execute() {
        echo $this->TestNoServiceExistModelNoMethod->get();
    }
}
