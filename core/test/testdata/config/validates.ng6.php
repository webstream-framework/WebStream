<?php
namespace WebStream;
// lengthにマイナス値が設定された場合
Validator::setRule(
    array(
        "test_validate#validate1" => array(
            "get#name" => "length[-100]"
        )
    )
);