<?php
namespace WebStream;
class SampleHelper extends CoreHelper {
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
    
    public function byIterator($list) {
        $mem1 = memory_get_usage();
        $count = 0;
        foreach ($list as $data) {
            $count++;
        }
        $mem2 = memory_get_usage();
        $html = "<div>data num: ${count}</div>";
        $html.= "<div>usage memory: " . ($mem2 - $mem1) / 1024 . "KB</div>";
        
        $ttt = 0;
        foreach ($list as $data) {
            $ttt++;
        }
        var_dump($ttt);
        
        return $html;
    }
    
    public function byArray($list) {
        $mem1 = memory_get_usage();
        $count = 0;
        $list = $list->toArray();
        foreach ($list as $data) {
            $count++;
        }
        $mem2 = memory_get_usage();
        $html = "<div>data num: ${count}</div>";
        $html.= "<div>usage memory: " . ($mem2 - $mem1) / 1024 . "KB</div>";
        return $html;
    }
}