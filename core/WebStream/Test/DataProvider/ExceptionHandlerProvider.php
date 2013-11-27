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
            ["/exception_handler6", "resource notfound error"]
        ];
    }

    public function failureErrorHandlingProvider()
    {
        return [
            ["/exception_handler7"],
            ["/exception_handler8"],
            ["/exception_handler9"],
            ["/exception_handler10"],
            ["/exception_handler11"]
        ];
    }
}
