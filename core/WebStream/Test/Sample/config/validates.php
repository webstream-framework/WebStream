<?php
namespace WebStream\Delegate;

/**
 * バリデーションルールを記述する
 */
Validator::setRule(
    [
        "test_validator#get_required" => [
            "get#name" => "required"
        ],
        "test_validator#get_min_length" => [
            "get#name" => "min_length[10]"
        ],
        "test_validator#get_max_length" => [
            "get#name" => "max_length[10]"
        ],
        "test_validator#get_min" => [
            "get#num" => "min[100]"
        ],
        "test_validator#get_max" => [
            "get#num" => "max[200]"
        ],
        "test_validator#get_equal" => [
            "get#name" => "equal[test]"
        ],
        "test_validator#get_length" => [
            "get#name" => "length[10]"
        ],
        "test_validator#get_range" => [
            "get#num" => "range[10..20]"
        ],
        "test_validator#get_regexp" => [
            "get#num" => "regexp[/^[0-9]{1}$/]"
        ],
        "test_validator#get_number" => [
            "get#num" => "number"
        ],
        "test_validator#post_required" => [
            "post#name" => "required"
        ],
        "test_validator#post_min_length" => [
            "post#name" => "min_length[10]"
        ],
        "test_validator#post_max_length" => [
            "post#name" => "max_length[10]"
        ],
        "test_validator#post_min" => [
            "post#num" => "min[100]"
        ],
        "test_validator#post_max" => [
            "post#num" => "max[200]"
        ],
        "test_validator#post_equal" => [
            "post#name" => "equal[test]"
        ],
        "test_validator#post_length" => [
            "post#name" => "length[10]"
        ],
        "test_validator#post_range" => [
            "post#num" => "range[10..20]"
        ],
        "test_validator#post_regexp" => [
            "post#num" => "regexp[/^[0-9]{1}$/]"
        ],
        "test_validator#post_number" => [
            "post#num" => "number"
        ],
        "test_validator#put_required" => [
            "put#name" => "required"
        ],
        "test_validator#put_min_length" => [
            "put#name" => "min_length[10]"
        ],
        "test_validator#put_max_length" => [
            "put#name" => "max_length[10]"
        ],
        "test_validator#put_min" => [
            "put#num" => "min[100]"
        ],
        "test_validator#put_max" => [
            "put#num" => "max[200]"
        ],
        "test_validator#put_equal" => [
            "put#name" => "equal[test]"
        ],
        "test_validator#put_length" => [
            "put#name" => "length[10]"
        ],
        "test_validator#put_range" => [
            "put#num" => "range[10..20]"
        ],
        "test_validator#put_regexp" => [
            "put#num" => "regexp[/^[0-9]{1}$/]"
        ],
        "test_validator#put_number" => [
            "put#num" => "number"
        ],






        "test_validator#get_param_validate1" => [
            "get#name1" => "required",
            "get#name2" => "min_length[10]",
            "get#name3" => "max_length[10]",
            "get#name4" => "min[100]",
            "get#name5" => "max[200]",
            "get#name6" => "equal[kyouko]",
            "get#name7" => "length[10]",
            "get#name8" => "range[10..20]",
            "get#name9" => "regexp[/^\d{1}$/]",
            "get#name10" => "number"
        ],
        "test_validator_error_handling#validate1" => [
            "get#name" => "required"
        ],
        "test_validator_error_handling2#validate1" => [
            "get#name" => "min_length[2]|equal[kyouko]"
        ]
    ]
);
