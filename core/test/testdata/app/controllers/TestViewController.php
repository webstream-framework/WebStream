<?php
namespace WebStream;
class TestViewController extends CoreController {
	/**
	 * @Inject
	 * @Render("test.tmpl")
	 */
    public function index() {}
    
    /**
     * @Inject
     * @Render("sub/test.tmpl")
     */
    public function subIndex() {}
    
    /**
     * @Inject
     * @Render("sub.test.tmpl")
     */
    public function index2() {}
}
