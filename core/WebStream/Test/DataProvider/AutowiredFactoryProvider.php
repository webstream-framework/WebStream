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
    public function autowiredProvider()
    {
        return [
            ["\WebStream\Test\TestData\AutowiredTestType", "kotori@lovelive.com", 17]
        ];
    }

    public function autowiredForConstantValueProvider()
    {
        return [
            ["honoka", 9]
        ];
    }
}
