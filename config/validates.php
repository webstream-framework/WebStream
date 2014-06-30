<?php
namespace WebStream\Delegate;

/**
 * バリデーションルールを記述する
 */
Validator::setRule([
    "sample#validate_form" => [
        "post#name" => "required"
    ]
]);
