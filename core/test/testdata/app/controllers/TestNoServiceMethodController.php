<?php
class TestNoServiceMethodController extends CoreController {
    public function execute() {
        echo $this->TestNoServiceMethod->get();
    }
}
