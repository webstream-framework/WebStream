<?php
namespace WebStream\Test\DataProvider;

/**
 * RouterProvider
 * @author Ryuichi TANAKA.
 * @since 2013/09/21
 * @version 0.4
 */
trait RouterProvider
{
    // 正常系
    public function resolvePathWithoutPlaceHolderProvider()
    {
        return [
            ["/", "test#test1", "test1"],
            ["/top", "test#test2", "test2"]
        ];
    }

    public function resolvePathWithPlaceHolderProvider()
    {
        return [
            ['/top/:id', "test#test3", "test3"]
        ];
    }

    public function resolveCamelActionProvider()
    {
        return [
            ['/action', "testAction"],
            ['/action2', "testAction2"]
        ];
    }

    public function resolveWithPlaceHolderFormatProvider()
    {
        return [
            ['/feed.rss']
        ];
    }

    public function snakeControllerProvider()
    {
        return [
            ["/snake", "snake"],
            ["/snake2", "snake2"]
        ];
    }

    public function uriWithEncodedStringProvider()
    {
        return [
            ['/encoded/%E3%81%A6%E3%81%99%E3%81%A8', 'てすと']
        ];
    }

    public function resolveSimilarUrlProvider()
    {
        return [
            ["/similar/name", "similar1"],
            ["/similar/name/2", "similar2"]
        ];
    }

    public function noServiceClass()
    {
        return [
            ["/no_service", "no service class"]
        ];
    }

    public function noServiceMethod()
    {
        return [
            ["/no_service2", "no service method"]
        ];
    }

    public function sendParamFromControllerToModelProvider()
    {
        return [
            ['/exist_service_exist_model_exist_model_method_param', "abc"],
            ['/exist_service_exist_model_exist_model_method_params', "abcdef"]
        ];
    }

    // 異常系
    public function resolveUnknownProvider()
    {
        return [
            ['/notfound/controller', "notfound#test"],
            ['/notfound/action', "test#notfound"]
        ];
    }

    public function multipleSnakeControllerProvider()
    {
        return [
            ["/snake_ng1"],
            ["/snake_ng2"]
        ];
    }

}
