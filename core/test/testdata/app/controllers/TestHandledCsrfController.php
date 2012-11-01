<?php
namespace WebStream;
class TestHandledCsrfController extends CoreController {
    /**
     * @Inject
     * @Security("CSRF")
     */
    public function showView() {
        $this->render("csrf"); 
    }
    
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