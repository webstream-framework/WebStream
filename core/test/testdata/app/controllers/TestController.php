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
    
    /**
     * @Inject
     * @Security("CSRF")
     * @Render("test.tmpl")
     */
    public function testCsrf() {}
    
    /**
     * @Inject
     * @Security("CSRF")
     * @Render("csrf_get.tmpl")
     */
    public function testCsrfGetView() {}
    
    public function testCsrfGet() {
        echo "csrf get is ok.";
    }
    
    /**
     * @Inject
     * @Security("CSRF")
     * @Render("csrf_post.tmpl")
     */
    public function testCsrfPostView() {}
    
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
        $this->__move(400);
    }
    
    public function test403() {
        $this->forbidden();
    }
    
    public function test404() {
        $this->__move(404);
    }
     
    public function test500() {
        $this->__move(500);
    }
    
    public function testUnknownStatusCode() {
        $this->__move(1000);
    }
    
    /**
     * @Inject
     * @Render("helper_html1.tmpl")
     */
    public function testHelperHtml1() {
        return array(
            "name" => "html"
        );
    }
    
    /**
     * @Inject
     * @Render("helper_html2.tmpl")
     */
    public function testHelperHtml2() {
        return array(
            "name" => "html"
        );
    }
    
    /**
     * @Inject
     * @Render("helper_string.tmpl")
     */
    public function testHelperString() {
        return array(
            "name" => "string"
        );
    }
    
    /**
     * @Inject
     * @Render("helper_snake.tmpl")
     */
    public function testHelperSnake() {}
    
    /**
     * @Inject
     * @Render("helper_camel.tmpl")
     */
    public function testHelperCamel() {}
    
    /**
     * @Inject
     * @Render("helper_notfound_method.tmpl")
     */
    public function testHelperNotfoundMethod() {}
    
    /**
     * @Inject
     * @Render("test_attr.tmpl")
     */
    public function testAttributeValue() {
        return array(
            "value" => "attr"
        );
    }
    
    /**
     * @Inject
     * @Format("json")
     */
    public function testJson() {
        return array("name" => "kyouko");
    }
    
    /**
     * @Inject
     * @Format("jsonp")
     * @Callback("__callback__")
     */
    public function testJsonp() {
        return array(
            "name" => "yui",
            "__callback__" => "yuruyuri"
        );
    }
}
