<?php
namespace WebStream;
class TestHandledSessionTimeoutController extends CoreController {
    public function showView() {
        Session::restart(3);
        $this->render("session_timeout");
    }
    
    /**
     * @Inject
     * @Error("SessionTimeout")
     */
    public function handle() {
        echo "handled session timeout.";
    }
}