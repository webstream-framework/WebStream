<?php
class TestController extends CoreController {
    public function test1() {
        echo "test1";
    }

    public function test2() {
        echo "test2";
    }
    
    public function test3($params) {
        echo $params["id"];
    }
    
    public function testAction() {
        echo "testAction";
    }
    
    public function testActionHogeFuga() {
        echo "testAction2";
    }
    
    public function testFeed($params) {
        echo $params["format"];
    }
    
    public function testCoreController1() {
        echo $this->Test instanceof CoreService;
    }
    
    public function testCsrf() {
        $this->render("test");
    }
    
    public function testCsrfPost() {
        echo "csrf post is ok.";
    }
    
    public function testEncoded($params) {
        echo $params["name"];
    }
    
    public function testSimilar1() {
        echo "similar1";
    }

    public function testSimilar2($params) {
        echo "similar". $params["page"];
    }
    
    public function testNotFoundRender() {
        $this->render_dummy("dummy");
    }
}
