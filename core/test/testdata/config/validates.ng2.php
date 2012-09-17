<?php
namespace WebStream;

Validator::setRule(
    array(
        "test#dummy" => array(
            "get#name" => "required"
        )
    )
);
