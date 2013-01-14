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
     * @Layout("base.tmpl")
     * @Render("index.tmpl", "index")
     */
    public function index($params) {
        return array(
            "title" => $this->title,
            "name" => "WebStream!"
        );
    }

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

    /**
     * @Inject
     * @Format("json")
     */
    public function annoRenderJson() {
        return array(
            "title" => "render test json"
        );
    }

    /**
     * @Inject
     * @Format("jsonp")
     * @Callback("__callback__")
     */
    public function annoRenderJsonp() {
        return array(
            "title" => "render test json",
            "__callback__" => "callback"
        );
    }

    /**
     * @Inject
     * @Layout("base.tmpl")
     * @Render("model1.tmpl", "index")
     */
    public function model1() {
        return array(
            "title" => $this->title,
            "data" => $this->Sample->model1()
        );
    }
    
    /**
     * @Inject
     * @Layout("base.tmpl")
     * @Render("model2.tmpl", "index")
     */
    public function model2() {
        return array(
            "title" => $this->title,
            "data" => $this->Sample->model2()
        );
    }

    /**
     * @Inject
     * @Layout("base.tmpl")
     * @Render("model3.tmpl", "index")
     */
    public function model3() {
        return array(
            "title" => $this->title,
            "data" => $this->Sample->model1()
        );
    }
    
    /**
     * @Inject
     * @Layout("base.tmpl")
     * @Render("index.helper.tmpl", "index")
     */
    public function helper() {
        return array(
            "title" => $this->title,
            "name" => "<script type='text/javascript'>alert('xss');</script>"
        );
    }
    
    /**
     * @Inject
     * @Layout("base.tmpl")
     * @Render("validate.tmpl", "index")
     */
    public function validate() {
        return array(
            "title" => $this->title
        );
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
