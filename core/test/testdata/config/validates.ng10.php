<?php
namespace WebStream;
Validator::setRule(
    array(
        "test_validate#validate1" => array(
            "get#name" => "range[00.00..10]"
        )
    )
);