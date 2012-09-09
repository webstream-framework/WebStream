<?php
namespace WebStream;
class TestExistServiceExistModelExistModelMethodModel extends CoreModel {
    public function get1($arg1) {
        echo $arg1;
    }
    
    public function get2($arg1, $arg2) {
        echo $arg1 . $arg2;
    }
}
