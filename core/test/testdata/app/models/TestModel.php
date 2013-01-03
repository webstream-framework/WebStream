<?php
namespace WebStream;
class TestModel extends CoreModel {
    public function testModelLayerInstance() {
        $controller = $this->__getController();
        $service = $this->__getService();
        $model = $this->__getModel();
        $view = $this->__getView();
        $helper = $this->__getHelper();
        echo is_null($controller);
        echo is_null($service);
        echo is_null($model);
        echo is_null($view);
        echo is_null($helper);
    }
}
