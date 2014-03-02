<?php
namespace WebStream\Test\DataProvider;

/**
 * ExceptionHandlerProvider
 * @author Ryuichi TANAKA.
 * @since 2013/11/27
 * @version 0.4
 */
trait ExceptionHandlerProvider
{
    public function successErrorHandlingProvider()
    {
        return [
            ["/exception_handler1", "validator error"],
            ["/exception_handler2", "forbidden access error"],
            ["/exception_handler3", "session timeout error"],
            ["/exception_handler4", "invalid request error"],
            ["/exception_handler5", "csrf error"],
            ["/exception_handler6", "resource notfound error"],
            ["/exception_handler8", "classnotfound error"],
            ["/exception_handler9", "methodnotfound error"],
            ["/exception_handler10", "annotation error"],
            ["/exception_handler11", "router error"]
        ];
    }

    public function successErrorMultipleHandlingProvider()
    {
        return [
            ["/multiple_exception_handler1", "125"],
            ["/multiple_exception_handler2", "125"],
            ["/multiple_exception_handler3", "235"],
            ["/multiple_exception_handler4", "235"],
            ["/multiple_exception_handler5", "245"],
            ["/multiple_exception_handler6", "25"],
            ["/double_exception_handler1", "1"],
            ["/double_exception_handler2", "1"]
        ];
    }

    public function failureErrorHandlingProvider()
    {
        return [
            ["/exception_handler7"]
        ];
    }
}
