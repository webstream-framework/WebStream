<?php
namespace WebStream;
// 存在しないルールが指定された場合
Validator::setRule(
    array(
        "test_validate#validate1" => array(
            "get#name" => "xxxxxxxxxx"
        )
    )
);

