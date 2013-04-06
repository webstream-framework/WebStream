<?php
namespace WebStream;
class TestSessionController extends CoreController {
    public function set() {
        $this->session->set("name", "test");
        echo $this->session->id();
    }
    
    public function get() {
        echo $this->session->get("name");
    }

    /**
     * @Inject
     * @Render("linkto.tmpl")
     */
    public function timeout() {
        $this->session->restart(1);
        return array(
            "link" => "/WebStream/core/test/testdata/dummy_link"
        );
    }

    /**
     * @Inject
     * @Render("linkto.tmpl")
     */
    public function timeoutLinkTo() {
    	$this->session->restart(1);
    	return array(
    		"link" => "/WebStream/core/test/testdata/dummy_link"
		);
    }
}
