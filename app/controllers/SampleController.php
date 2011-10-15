<?php
/**
 * サンプル
 */
class SampleController extends AppController {
    
    private $title;
    
    public function before() {
        $title = "stream sample";
    }
    
    public function after() {}
    
    public function index($params) {
        $this->layout("base", array(
            "title" => $this->title,
            "template" => "index",
            "content" => array(
                "name" => "stream framework"
            )
        ));
    }
}
