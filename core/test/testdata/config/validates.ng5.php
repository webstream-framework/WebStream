<?php
namespace WebStream;
// max_lengthにマイナス値が設定された場合
Validator::setRule(
    array(
        "test_validate#validate1" => array(
            "get#name" => "max_length[-100]"
        )
    )
);