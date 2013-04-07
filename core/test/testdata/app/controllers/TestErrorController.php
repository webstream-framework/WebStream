<?php
namespace WebStream;
class TestErrorController extends CoreController {
    public function csrfError() {
        throw new CsrfException("csrf error");
    }

    public function validateError() {
        throw new ValidateException("validate error");
    }

    public function sessionTimeoutError() {
        throw new SessionTimeoutException("session timeout");
    }

    public function methodNotAllowedError() {
        throw new MethodNotAllowedException("method not allowed");
    }

    public function forbiddenAccessError() {
        throw new ForbiddenAccessException("forbidden access");
    }

    public function resourceNotFoundError() {
        throw new ResourceNotFoundException("resource not found");
    }

    public function otherError() {
        throw new \Exception("other error");
    }

    /**
     * @Inject
     * @Error("CSRF")
     */
    public function csrfErrorHandle($e) {
        echo $e->getMessage();
    }

    /**
     * @Inject
     * @Error("Validate")
     */
    public function validateErrorHandle($e) {
        echo $e->getMessage();
    }

    /**
     * @Inject
     * @Error("SessionTimeout")
     */
    public function sessionTimeoutErrorHandle($e) {
        echo $e->getMessage();
    }

    /**
     * @Inject
     * @Error("MethodNotAllowed")
     */
    public function methodNotAllowedErrorHandle($e) {
        echo $e->getMessage();
    }

    /**
     * @Inject
     * @Error("ForbiddenAccess")
     */
    public function forbiddenAccessErrorHandle($e) {
         echo $e->getMessage();
    }

    /**
     * @Inject
     * @Error("ResourceNotFound")
     */
    public function resourceNotFoundErrorHandle($e) {
         echo $e->getMessage();
    }

    /**
     * @Inject
     * @Error
     */
    public function commonError($e) {
        echo "!";
    }

}