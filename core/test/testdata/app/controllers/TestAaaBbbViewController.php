<?php
namespace WebStream;
class TestAaaBbbViewController extends CoreController {
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
}
