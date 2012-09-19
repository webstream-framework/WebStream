<?php
namespace WebStream;
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
    
    public function testCsrfGetView() {
        $this->render("csrf_get");
    }
    
    public function testCsrfGet() {
        echo "csrf get is ok.";
    }
    
    public function testCsrfPostView() {
        $this->render("csrf_post");
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

    public function test301() {
        $this->redirect("http://www.yahoo.co.jp");
    }
    
    public function test400() {
        $this->move(400);
    }
    
    public function test403() {
        $this->forbidden();
    }
    
    public function test404() {
        $this->move(404);
    }
     
    public function test500() {
        $this->move(500);
    }
    
    public function testUnknownStatusCode() {
        $this->move(1000);
    }
    
    public function testHelperHtml1() {
        $this->render("helper_html1", array(
            "name" => "html"
        ));
    }
    
    public function testHelperHtml2() {
        $this->render("helper_html2", array(
            "name" => "html"
        ));
    }
    
    public function testHelperString() {
        $this->render("helper_string", array(
            "name" => "string"
        ));
    }
    
    public function testHelperSnake() {
        $this->render("helper_snake");
    }
    
    public function testHelperCamel() {
        $this->render("helper_camel");
    }
    
    public function testHelperNotfoundMethod() {
        $this->render("helper_notfound_method");
    }
    
    public function testAttributeValue() {
        $this->render("test_attr", array(
            "value" => "attr"
        ));
    }
    
    public function testJson() {
        $this->render_json(array("name" => "kyouko"));
    }
    
    public function testJsonp() {
        $this->render_jsonp(array("name" => "yui"), "yuruyuri");
    }
}
