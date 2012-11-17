<?php
namespace WebStream;
class TestHandledCsrfController extends CoreController {
    /**
     * @Inject
     * @Security("CSRF")
     * @Render("csrf.tmpl")
     */
    public function showView() {}
    
    public function execute() {
        echo "ok.";
    }
    
    /**
     * @Inject
     * @Error("CSRF")
     */
    public function handle() {
        echo "handled csrf.";
    }
}