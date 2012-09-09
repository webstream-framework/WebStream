<?php
namespace WebStream;
class TestRequestController extends CoreController {
    public function get() {
        echo $this->request->get("name");
    }
    
    public function post() {
        echo $this->request->post("name");
    }
}
