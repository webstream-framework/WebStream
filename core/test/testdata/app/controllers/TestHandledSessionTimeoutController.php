<?php
namespace WebStream;
class TestHandledSessionTimeoutController extends CoreController {
    public function showView() {
        var_dump($_SESSION);
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