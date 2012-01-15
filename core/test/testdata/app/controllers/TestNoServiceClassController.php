<?php
class TestNoServiceClassController extends CoreController {
    public function execute() {
        echo $this->TestNoServiceClass->get();
    }
}
