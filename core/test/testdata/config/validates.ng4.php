<?php
namespace WebStream;
// min_lengthにマイナス値が設定された場合
Validator::setRule(
    array(
        "test_validate#validate1" => array(
            "get#name" => "min_length[-100]"
        )
    )
);