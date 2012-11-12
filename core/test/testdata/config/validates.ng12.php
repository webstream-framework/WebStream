<?php
namespace WebStream;
Validator::setRule(
    array(
        "test_validate#validate1" => array(
            "get#name" => "min[00.10]"
        )
    )
);