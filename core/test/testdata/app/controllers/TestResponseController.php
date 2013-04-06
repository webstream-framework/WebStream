<?php
namespace WebStream;
class TestResponseController extends CoreController {
    /**
     * @Inject
     * @Response("201")
     */
    public function created() {}

    /**
     * @Inject
     * @Response("201", "204")
     */
    public function invalid() {}

    /**
     * @Inject
     * @Response("999")
     */
    public function unknown() {}
}