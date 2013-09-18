<?php
namespace WebStream\Test\DataProvider;

/**
 * AutowiredFactoryProvider
 * @author Ryuichi TANAKA.
 * @since 2013/09/18
 * @version 0.4
 */
trait AutowiredFactoryProvider
{
    public function autowiredForValueProvider()
    {
        return [
            ["mail", "kotori@lovelive.com"],
            ["age", 17]
        ];
    }

    public function autowiredForConstantValueProvider()
    {
        return [
            ["name", "honoka"],
            ["memberNum", 9]
        ];
    }

    public function autowiredInvalidTypeProvider()
    {
        return [
            ["\WebStream\Test\TestData\AutowiredTest2"]
        ];
    }
}
