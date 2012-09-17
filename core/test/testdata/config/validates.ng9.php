<?php
namespace WebStream;
// get,post,put,delete以外のメソッドが指定
Validator::setRule(
    array(
        "test_validate#validate1" => array(
            "dummy#name" => "required"
        )
    )
);