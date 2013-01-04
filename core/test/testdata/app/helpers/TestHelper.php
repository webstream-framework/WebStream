<?php
namespace WebStream;
class TestHelper extends CoreHelper {
    public function showHtml1($name) {
        return <<< HELPER
        <div class="test">$name</div>
HELPER;
    }
    
    public function showHtml2($name) {
        return "<div class=\"test\">$name</div>";
    }
    
    public function showString($name) {
        return '<div class="test">$name</div>';
    }
    
    public function showSnakeCamel() {
        return "test";
    }

    public function layerInstance() {
        $controller = $this->__getController();
        $service = $this->__getService();
        $model = $this->__getModel();
        $view = $this->__getView();
        $helper = $this->__getHelper();
        echo is_null($controller);
        echo is_null($service);
        echo is_null($model);
        echo is_object($view);
        echo is_null($helper);
    }

    public function templateInHelper($name) {
        return "@{test_template}";
    }
}