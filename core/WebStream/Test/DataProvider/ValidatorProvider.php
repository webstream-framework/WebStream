<?php
namespace WebStream\Test\DataProvider;

/**
 * ValidatorProvider
 * @author Ryuichi TANAKA.
 * @since 2013/11/16
 * @version 0.4
 */
trait ValidatorProvider {
    public function validatorProvider()
    {
        return [
            ["/get_ok_validate1", ["name" => "test"]],
            ["/get_ok_validate2", ["name" => "aaaaaaaaaa"]],
            ["/get_ok_validate3", ["name" => "aaaaaaaaaa"]],
            ["/get_ok_validate4", ["num" => 100]],
            ["/get_ok_validate5", ["num" => 200]],
            ["/get_ok_validate6", ["name" => "test"]],
            ["/get_ok_validate7", ["name" => "abcdefghij"]],
            ["/get_ok_validate8", ["num" => 12]],
            ["/get_ok_validate9", ["num" => 9]],
            ["/get_ok_validate10", ["num" => 1000]],
            ["/get_ok_validate11", ["num" => 123.456]]
        ];
    }

    public function validatorErrorProvider() {
        return [
            ["/get_ok_validate1", ["name" => "test"]],
            ["/get_ok_validate2", ["name" => "aaaaaaaaaa"]],
            ["/get_ok_validate3", ["name" => "aaaaaaaaaa"]],
        ];
    }
}
