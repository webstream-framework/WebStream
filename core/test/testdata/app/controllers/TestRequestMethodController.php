<?php
namespace WebStream;
class TestRequestMethodController extends CoreController {
    /**
     * @Inject
     * @Request("GET")
     */
    public function getOnly() {
        echo "get only";
    }

    /**
     * @Inject
     * @Request("POST")
     */
    public function postOnly() {
        echo "post only";
    }

    /**
     * @Inject
     * @Request("GET", "POST")
     */
    public function availableGetPost() {
        echo "get or post";
    }
}