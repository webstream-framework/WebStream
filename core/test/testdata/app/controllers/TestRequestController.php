<?php
namespace WebStream;
class TestRequestController extends CoreController {
    /**
     * @Inject
     * @Request("GET")
     */
    public function get() {
        echo $this->request->get("name");
    }
    
    /**
     * @Inject
     * @Request("POST")
     */
    public function post() {
        echo $this->request->post("name");
    }

    /**
     * @Inject
     * @Request("PUT")
     */
    public function put() {
        echo $this->request->put("name");
    }

    /**
     * @Inject
     * @Request("DELETE")
     */
    public function delete() {
        echo $this->request->delete("name");
    }
}
