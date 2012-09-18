<?php
namespace WebStream;

Validator::setRule(
    array(
        "sample#validate_form" => array(
            "post#name" => "required"
        ) 
    )
);
