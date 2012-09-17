<?php
namespace WebStream;
// rangeの範囲設定が逆転している
Validator::setRule(
    array(
        "test_validate#validate1" => array(
            "get#name" => "range[100..90]"
        )
    )
);