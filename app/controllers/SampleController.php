<?php
namespace WebStream;
/**
 * サンプル
 */
class SampleController extends AppController {
    private $title;
    
    public function before() {
        $title = "stream sample";
    }
    
    public function after() {}
    
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
}
