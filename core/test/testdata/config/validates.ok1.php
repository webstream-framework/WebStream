<?php
namespace WebStream;

Validator::setRule(
    array(
        "test_validate#validate1" => array(
            "get#name1" => "required",
            "get#name2" => "min_length[10]",
            "get#name3" => "max_length[10]",
            "get#name4" => "max[10]",
            "get#name5" => "min[10]",
            "get#name6" => "equal[kyouko]",
            "get#name7" => "length[10]",
            "get#name8" => "regexp[/^¥d{10}$/]",
            "get#name9" => "required|max_length[10]",
            "get#name10" => "max_length[10]|equal[kyouko]",
            "get#name11" => "max[10]|min_length[10]",
            "get#name12" => "length[10]|required|max[10]",
            "get#name13" => "regexp[/^¥d{10}$/]|max_length[10]|mix[10]",
            "get#name14" => "max[-10]|min[-100]",
            "get#name15" => "max[-10.0]|min[-100.0]",
            "get#name16" => "max[0.111111]|min[0.23455]",
            "get#name17" => "number"
        ) 
    )
);
