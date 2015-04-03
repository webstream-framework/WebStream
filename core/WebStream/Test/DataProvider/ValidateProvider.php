<?php
namespace WebStream\Test\DataProvider;

/**
 * ValidateProvider
 * @author Ryuichi TANAKA.
 * @since 2014/04/01
 * @version 0.4
 */
trait ValidateProvider
{
    public function validProvider()
    {
        return [
            ["/test_validate/required/get", "get", "test", "honoka"],
            ["/test_validate/required/post", "post", "test", "honoka"],
            ["/test_validate/required/put", "put", "test", "honoka"],
            ["/test_validate/required/all", "all", "test", "honoka"],
            ["/test_validate/equal/get", "get", "test", "honoka"],
            ["/test_validate/equal/post", "post", "test", "honoka"],
            ["/test_validate/equal/put", "put", "test", "honoka"],
            ["/test_validate/equal/all", "all", "test", "honoka"],
            ["/test_validate/length/get", "get", "test", "honoka"],
            ["/test_validate/length/post", "post", "test", "honoka"],
            ["/test_validate/length/put", "put", "test", "honoka"],
            ["/test_validate/length/all", "all", "test", "honoka"],
            ["/test_validate/length/get", "get", "test", "ほのかちゃん"],
            ["/test_validate/length/post", "post", "test", "ほのかちゃん"],
            ["/test_validate/length/put", "put", "test", "ほのかちゃん"],
            ["/test_validate/length/all", "all", "test", "ほのかちゃん"],
            ["/test_validate/max/get", "get", "test", "2"],
            ["/test_validate/max/post", "post", "test", "2"],
            ["/test_validate/max/put", "put", "test", "2"],
            ["/test_validate/max/all", "all", "test", "2"],
            ["/test_validate/min/get", "get", "test", "2"],
            ["/test_validate/min/post", "post", "test", "2"],
            ["/test_validate/min/put", "put", "test", "2"],
            ["/test_validate/min/all", "all", "test", "2"],
            ["/test_validate/max_length/get", "get", "test", "honoka"],
            ["/test_validate/max_length/post", "post", "test", "honoka"],
            ["/test_validate/max_length/put", "put", "test", "honoka"],
            ["/test_validate/max_length/all", "all", "test", "honoka"],
            ["/test_validate/max_length/get", "get", "test", "ほのかちゃん"],
            ["/test_validate/max_length/post", "post", "test", "ほのかちゃん"],
            ["/test_validate/max_length/put", "put", "test", "ほのかちゃん"],
            ["/test_validate/max_length/all", "all", "test", "ほのかちゃん"],
            ["/test_validate/min_length/get", "get", "test", "honoka"],
            ["/test_validate/min_length/post", "post", "test", "honoka"],
            ["/test_validate/min_length/put", "put", "test", "honoka"],
            ["/test_validate/min_length/all", "all", "test", "honoka"],
            ["/test_validate/min_length/get", "get", "test", "ほのかちゃん"],
            ["/test_validate/min_length/post", "post", "test", "ほのかちゃん"],
            ["/test_validate/min_length/put", "put", "test", "ほのかちゃん"],
            ["/test_validate/min_length/all", "all", "test", "ほのかちゃん"],
            ["/test_validate/number/get", "get", "test", "2"],
            ["/test_validate/number/post", "post", "test", "2"],
            ["/test_validate/number/put", "put", "test", "2"],
            ["/test_validate/number/all", "all", "test", "2"],
            ["/test_validate/number/get", "get", "test", "0.12345"],
            ["/test_validate/number/post", "post", "test", "0.12345"],
            ["/test_validate/number/put", "put", "test", "0.12345"],
            ["/test_validate/number/all", "all", "test", "0.12345"],
            ["/test_validate/number/get", "get", "test", "-1.2"],
            ["/test_validate/number/post", "post", "test", "-1.2"],
            ["/test_validate/number/put", "put", "test", "-1.2"],
            ["/test_validate/number/all", "all", "test", "-1.2"],
            ["/test_validate/range/get", "get", "test", "3"],
            ["/test_validate/range/post", "post", "test", "3"],
            ["/test_validate/range/put", "put", "test", "3"],
            ["/test_validate/range/all", "all", "test", "3"],
            ["/test_validate/regexp/get", "get", "test", "3"],
            ["/test_validate/regexp/post", "post", "test", "3"],
            ["/test_validate/regexp/put", "put", "test", "3"],
            ["/test_validate/regexp/all", "all", "test", "3"]
        ];
    }

    public function validateErrorProvider()
    {
        return [
            ["/test_validate/required/get", "get", "test", ""],
            ["/test_validate/required/post", "post", "test", ""],
            ["/test_validate/required/put", "put", "test", ""],
            ["/test_validate/required/all", "all", "test", ""],
            ["/test_validate/required/get", "get"],
            ["/test_validate/required/post", "post"],
            ["/test_validate/required/put", "put"],
            ["/test_validate/required/all", "all"],
            ["/test_validate/equal/get", "get", "test", "kotori"],
            ["/test_validate/equal/post", "post", "test", "kotori"],
            ["/test_validate/equal/put", "put", "test", "kotori"],
            ["/test_validate/equal/all", "all", "test", "kotori"],
            ["/test_validate/length/get", "get", "test", "umichang"],
            ["/test_validate/length/post", "post", "test", "umichang"],
            ["/test_validate/length/put", "put", "test", "umichang"],
            ["/test_validate/length/all", "all", "test", "umichang"],
            ["/test_validate/max/get", "get", "test", "4"],
            ["/test_validate/max/post", "post", "test", "4"],
            ["/test_validate/max/put", "put", "test", "4"],
            ["/test_validate/max/all", "all", "test", "4"],
            ["/test_validate/min/get", "get", "test", "-1"],
            ["/test_validate/min/post", "post", "test", "-1"],
            ["/test_validate/min/put", "put", "test", "-1"],
            ["/test_validate/min/all", "all", "test", "-1"],
            ["/test_validate/max/get", "get", "test", "4"],
            ["/test_validate/max/post", "post", "test", "4"],
            ["/test_validate/max/put", "put", "test", "4"],
            ["/test_validate/max/all", "all", "test", "4"],
            ["/test_validate/max_length/get", "get", "test", "umichang"],
            ["/test_validate/max_length/post", "post", "test", "umichang"],
            ["/test_validate/max_length/put", "put", "test", "umichang"],
            ["/test_validate/max_length/all", "all", "test", "umichang"],
            ["/test_validate/min_length/get", "get", "test", "maki"],
            ["/test_validate/min_length/post", "post", "test", "maki"],
            ["/test_validate/min_length/put", "put", "test", "maki"],
            ["/test_validate/min_length/all", "all", "test", "maki"],
            ["/test_validate/number/get", "get", "test", "kotori"],
            ["/test_validate/number/post", "post", "test", "kotori"],
            ["/test_validate/number/put", "put", "test", "kotori"],
            ["/test_validate/number/all", "all", "test", "kotori"],
            ["/test_validate/range/get", "get", "test", "11"],
            ["/test_validate/range/post", "post", "test", "11"],
            ["/test_validate/range/put", "put", "test", "11"],
            ["/test_validate/range/all", "all", "test", "11"],
            ["/test_validate/regexp/get", "get", "test", "honoka"],
            ["/test_validate/regexp/post", "post", "test", "honoka"],
            ["/test_validate/regexp/put", "put", "test", "honoka"],
            ["/test_validate/regexp/all", "all", "test", "honoka"]
        ];
    }

    public function invalidRuleProvider()
    {
        return [
            ["/test_validate/invalid_rule/unknown", "WebStream\Exception\Extend\AnnotationException"],
            ["/test_validate/invalid_rule/required", "WebStream\Exception\Extend\ValidateException"],
            ["/test_validate/invalid_rule/equal", "WebStream\Exception\Extend\ValidateException"],
            ["/test_validate/invalid_rule/length1", "WebStream\Exception\Extend\ValidateException"],
            ["/test_validate/invalid_rule/length2", "WebStream\Exception\Extend\ValidateException"],
            ["/test_validate/invalid_rule/length3", "WebStream\Exception\Extend\ValidateException"],
            ["/test_validate/invalid_rule/max", "WebStream\Exception\Extend\ValidateException"],
            ["/test_validate/invalid_rule/min", "WebStream\Exception\Extend\ValidateException"],
            ["/test_validate/invalid_rule/max_length", "WebStream\Exception\Extend\ValidateException"],
            ["/test_validate/invalid_rule/min_length", "WebStream\Exception\Extend\ValidateException"],
            ["/test_validate/invalid_rule/number", "WebStream\Exception\Extend\ValidateException"],
            ["/test_validate/invalid_rule/range1", "WebStream\Exception\Extend\ValidateException"],
            ["/test_validate/invalid_rule/range2", "WebStream\Exception\Extend\ValidateException"],
            ["/test_validate/invalid_rule/regexp", "WebStream\Exception\Extend\ValidateException"]
        ];
    }

    public function invalidAnnotationProvider()
    {
        return [
            ["/test_validate/invalid_annotation1", "WebStream\Exception\Extend\ValidateException"],
            ["/test_validate/invalid_annotation2", "WebStream\Exception\Extend\ValidateException"]
        ];
    }

}
