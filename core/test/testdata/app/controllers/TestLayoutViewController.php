<?php
class TestLayoutViewController extends CoreController {
    public function index() {
        $this->layout("test");
    }
    
    public function subIndex() {
        $this->layout("sub/test");
    }
}
