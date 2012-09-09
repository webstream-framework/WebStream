<?php
namespace WebStream;
class TestNoServiceAndModelController extends CoreController {
    public function execute() {
        $this->TestNoServiceAndModel->get();
    }
}
