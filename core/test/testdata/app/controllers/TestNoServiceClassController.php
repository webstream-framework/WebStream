<?php
namespace WebStream;
class TestNoServiceClassController extends CoreController {
    public function execute() {
        echo $this->TestNoServiceClass->get();
    }
}
