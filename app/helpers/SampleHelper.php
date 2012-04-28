<?php
class SampleHelper extends AppHelper {
    public function show1($name) {
        return <<< HELPER
        <div class="test">$name</div>
HELPER;
    }
    
    public function show2($name) {
        return '<div class="test">$name</div>';
    }
    
    public function showSnake() {
        return "test";
    }
}