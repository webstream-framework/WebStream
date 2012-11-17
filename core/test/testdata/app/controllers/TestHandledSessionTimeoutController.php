<?php
namespace WebStream;
class TestHandledSessionTimeoutController extends CoreController {
    /**
     * @Inject
     * @Render("session_timeout.tmpl")
     */
    public function showView() {
        Session::restart(3);
    }
    
    /**
     * @Inject
     * @Error("SessionTimeout")
     */
    public function handle() {
        echo "handled session timeout.";
    }
}