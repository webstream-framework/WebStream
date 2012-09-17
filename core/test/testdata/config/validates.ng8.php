<?php
namespace WebStream;
// rangeの範囲設定が文字列
Validator::setRule(
    array(
        "test_validate#validate1" => array(
            "get#name" => "range[aaa..bbb]"
        )
    )
);