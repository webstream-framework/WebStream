<?php
namespace WebStream;
class TestResourceController extends CoreController {
    public function html() {
        $this->render("html");
    }
    
    public function rss() {
        $this->render_rss("rss");
    }
    
    public function xml() {
        $this->render_xml("xml");
    }
    
    public function rdf() {
        $this->render_xml("rdf");
    }
    
    public function atom() {
        $this->render_atom("atom");
    }
}
