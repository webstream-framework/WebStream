<?php
namespace WebStream;
class TestLayoutViewController extends CoreController {
	/**
	 * @Inject
	 * @Layout("test.tmpl")
	 */
    public function index() {}
    
	/**
	 * @Inject
	 * @Layout("sub/test.tmpl")
	 */
    public function subIndex() {}
}
