<?php
class TestViewController extends CoreController {
    public function index() {
        $this->render("test");
    }
    
    public function subIndex() {
        $this->render("sub/test");
    }
}
