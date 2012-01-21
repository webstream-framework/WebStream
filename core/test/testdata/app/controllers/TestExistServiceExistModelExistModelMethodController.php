<?php
class TestExistServiceExistModelExistModelMethodController extends CoreController {
    public function sendParam() {
        $this->TestExistServiceExistModelExistModelMethod->get1("abc");
    }
    
    public function sendParams() {
        $this->TestExistServiceExistModelExistModelMethod->get2("abc", "def");
    }
}
