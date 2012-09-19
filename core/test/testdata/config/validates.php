<?php
namespace WebStream;

Validator::setRule(
    array(
        "test_validate#get_param_validate1" => array(
            "get#name1" => "required",
            "get#name2" => "min_length[10]",
            "get#name3" => "max_length[10]",
            "get#name4" => "min[100]",
            "get#name5" => "max[200]",
            "get#name6" => "equal[kyouko]",
            "get#name7" => "length[10]",
            "get#name8" => "range[10..20]",
            "get#name9" => "regexp[/^\d{1}$/]"
        ),
        "test_validate_error_handling#validate1" => array(
            "get#name" => "required"
        ),
        "test_validate_error_handling2#validate1" => array(
            "get#name" => "min_length[2]|equal[kyouko]"
        )
    )
);
