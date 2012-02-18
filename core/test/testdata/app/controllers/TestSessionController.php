<?php
class TestSessionController extends CoreController {
    public function init() {
        
    }
    
    public function set() {
        $this->session->set("name", "test");
        echo $this->session->id();
    }
    
    public function get() {
        echo $this->session->get("name");
    }
}
