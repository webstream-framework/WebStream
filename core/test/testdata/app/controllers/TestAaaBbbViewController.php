<?php
namespace WebStream;
class TestAaaBbbViewController extends CoreController {
    public function index() {
        $this->render("test");
    }
    
    public function subIndex() {
        $this->render("sub/test");
    }
}
