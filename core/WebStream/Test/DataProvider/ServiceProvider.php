<?php
namespace WebStream\Test\DataProvider;

/**
 * ServiceProvider
 * @author Ryuichi TANAKA.
 * @since 2013/12/07
 * @version 0.4
 */
trait ServiceProvider
{
    public function serviceProvider()
    {
        return [
            ["/test_service1", "Music S.T.A.R.T!!"],
            ["/test_service2", "Show Halation"]
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
}
