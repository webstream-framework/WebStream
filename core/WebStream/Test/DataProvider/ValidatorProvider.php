<?php
namespace WebStream\Test\DataProvider;

/**
 * ValidatorProvider
 * @author Ryuichi TANAKA.
 * @since 2013/11/16
 * @version 0.4
 */
trait ValidatorProvider
{
    public function validatorGetProvider()
    {
        return [
            ["/get_validate1", ["name" => "test"]],
            ["/get_validate2", ["name" => "aaaaaaaaaaa"]],
            ["/get_validate2", ["name" => "ああああああああああ"]],
            ["/get_validate3", ["name" => "aaaaaaaaaa"]],
            ["/get_validate2", ["name" => "ああああああああああ"]],
            ["/get_validate3", ["name" => "aaaaaaaaa"]],
            ["/get_validate4", ["num" => 100]],
            ["/get_validate4", ["num" => 101]],
            ["/get_validate5", ["num" => 199]],
            ["/get_validate5", ["num" => 200]],
            ["/get_validate6", ["name" => "test"]],
            ["/get_validate7", ["name" => "abcdefghij"]],
            ["/get_validate7", ["name" => "ああああああああああ"]],
            ["/get_validate8", ["num" => 12]],
            ["/get_validate9", ["num" => 9]],
            ["/get_validate10", ["num" => 1000]],
            ["/get_validate10", ["num" => "1000"]]
        ];
    }

    public function validatorPostProvider()
    {
        return [
            ["/post_validate1", ["name" => "test"]],
            ["/post_validate2", ["name" => "aaaaaaaaaaa"]],
            ["/post_validate2", ["name" => "ああああああああああ"]],
            ["/post_validate3", ["name" => "aaaaaaaaaa"]],
            ["/post_validate2", ["name" => "ああああああああああ"]],
            ["/post_validate3", ["name" => "aaaaaaaaa"]],
            ["/post_validate4", ["num" => 100]],
            ["/post_validate4", ["num" => 101]],
            ["/post_validate5", ["num" => 199]],
            ["/post_validate5", ["num" => 200]],
            ["/post_validate6", ["name" => "test"]],
            ["/post_validate7", ["name" => "abcdefghij"]],
            ["/post_validate7", ["name" => "ああああああああああ"]],
            ["/post_validate8", ["num" => 12]],
            ["/post_validate9", ["num" => 9]],
            ["/post_validate10", ["num" => 1000]],
            ["/post_validate10", ["num" => "1000"]]
        ];
    }

    public function validatorPutProvider()
    {
        return [
            ["/put_validate1", ["name" => "test"]],
            ["/put_validate2", ["name" => "aaaaaaaaaaa"]],
            ["/put_validate2", ["name" => "ああああああああああ"]],
            ["/put_validate3", ["name" => "aaaaaaaaaa"]],
            ["/put_validate2", ["name" => "ああああああああああ"]],
            ["/put_validate3", ["name" => "aaaaaaaaa"]],
            ["/put_validate4", ["num" => 100]],
            ["/put_validate4", ["num" => 101]],
            ["/put_validate5", ["num" => 199]],
            ["/put_validate5", ["num" => 200]],
            ["/put_validate6", ["name" => "test"]],
            ["/put_validate7", ["name" => "abcdefghij"]],
            ["/put_validate7", ["name" => "ああああああああああ"]],
            ["/put_validate8", ["num" => 12]],
            ["/put_validate9", ["num" => 9]],
            ["/put_validate10", ["num" => 1000]],
            ["/put_validate10", ["num" => "1000"]]
        ];
    }

    public function validatorGetErrorProvider()
    {
        return [
            ["/get_validate1", ["name" => ""]],
            ["/get_validate2", ["name" => "aaaaaaaaa"]],
            ["/get_validate2", ["name" => "あああああああああ"]],
            ["/get_validate3", ["name" => "aaaaaaaaaaa"]],
            ["/get_validate3", ["name" => "あああああああああああ"]],
            ["/get_validate4", ["num" => 99]],
            ["/get_validate4", ["num" => 99.9]],
            ["/get_validate5", ["num" => 201]],
            ["/get_validate5", ["num" => 200.1]],
            ["/get_validate6", ["name" => "dummy"]],
            ["/get_validate7", ["name" => "abcdefghijk"]],
            ["/get_validate8", ["num" => 9]],
            ["/get_validate8", ["num" => 9.9]],
            ["/get_validate8", ["num" => 20.1]],
            ["/get_validate8", ["num" => 21]],
            ["/get_validate9", ["num" => 10]],
            ["/get_validate9", ["num" => "a"]],
            ["/get_validate10", ["num" => "a"]]
        ];
    }

    public function validatorPostErrorProvider()
    {
        return [
            ["/post_validate1", ["name" => ""]],
            ["/post_validate2", ["name" => "aaaaaaaaa"]],
            ["/post_validate2", ["name" => "あああああああああ"]],
            ["/post_validate3", ["name" => "aaaaaaaaaaa"]],
            ["/post_validate3", ["name" => "あああああああああああ"]],
            ["/post_validate4", ["num" => 99]],
            ["/post_validate4", ["num" => 99.9]],
            ["/post_validate5", ["num" => 201]],
            ["/post_validate5", ["num" => 200.1]],
            ["/post_validate6", ["name" => "dummy"]],
            ["/post_validate7", ["name" => "abcdefghijk"]],
            ["/post_validate8", ["num" => 9]],
            ["/post_validate8", ["num" => 9.9]],
            ["/post_validate8", ["num" => 20.1]],
            ["/post_validate8", ["num" => 21]],
            ["/post_validate9", ["num" => 10]],
            ["/post_validate9", ["num" => "a"]],
            ["/post_validate10", ["num" => "a"]]
        ];
    }
}
