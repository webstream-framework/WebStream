<?php
namespace WebStream;
/**
 * サンプル
 */
class SampleController extends AppController {
    private $title;
    
    /**
     * @Inject
     * @Filter("Before")
     */
    public function before() {
        $this->title = "stream sample";
    }
    
    /**
     * @Inject
     * @Filter("After")
     */
    public function after() {}

    /**
     * @Inject
     * @Layout("base2.tmpl")
     * @Render("render.tmpl", "render_template")
     * @Render("render2.tmpl", "child_template")
     */
    public function annoRender() {
        return array(
            "title" => "render test",
            "text" => "新しいRender"
        );
    }

    public function model1() {
        $this->layout("base", array(
            "title" => $this->title,
            "template" => "model1",
            "content" => array(
                "data" => $this->Sample->model1()
            )
        ));
    }
    
    public function model2() {
        $this->layout("base", array(
            "title" => $this->title,
            "template" => "model2",
            "content" => array(
                "data" => $this->Sample->model2()
            )
        ));
    }

    public function model3() {
        $this->layout("base", array(
            "title" => $this->title,
            "template" => "model3",
            "content" => array(
                "data" => $this->Sample->model1()
            )
        ));
    }
    
    public function index($params) {
        $this->layout("base", array(
            "title" => $this->title,
            "template" => "index",
            "content" => array(
                "name" => "WebStream!"
            )
        ));
    }
    
    public function helper() {
        $this->layout("base", array(
            "title" => $this->title,
            "template" => "index.helper",
            "content" => array(
                "name" => "<script type='text/javascript'>alert('xss');</script>"
            )
        ));
    }
    
    public function validate() {
        $this->layout("base", array(
            "title" => $this->title,
            "template" => "validate",
            "content" => null
        ));
    }
    
    public function validateForm() {
        echo $this->request->post("name");
    }
    
    /**
     * @Inject
     * @BasicAuth("config/basic_auth.ini")
     */
    public function basicAuth() {
        echo "auth ok";
    }
    
    /**
     * @Inject
     * @Cache("60")
     */
    public function responseCache() {
        echo "response cache";
    }
}
