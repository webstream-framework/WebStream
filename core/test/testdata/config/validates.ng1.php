<?php
namespace WebStream;

Validator::setRule(
    array(
        "dummy#index" => array(
            "get#name" => "required"
        )
    )
);
