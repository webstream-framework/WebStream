<?php
namespace WebStream;
class TestHelper {
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
}