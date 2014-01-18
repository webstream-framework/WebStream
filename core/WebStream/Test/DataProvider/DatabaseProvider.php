<?php
namespace WebStream\Test\DataProvider;

/**
 * DatabaseProvider
 * @author Ryuichi TANAKA.
 * @since 2014/01/19
 * @version 0.4
 */
trait DatabaseProvider
{
    public function selectProvider()
    {
        return [
            ["/test_model1", "honoka"],
            ["/test_model2", "honoka"],
            ["/test_model4", "honoka"],
            ["/test_model5", "honoka"]
        ];
    }

    public function commitProvider()
    {
        return [
            ["/test_model3", "kotori"]
        ];
    }

    public function rollbackProvider()
    {
        return [
            ["/test_model7", "0"]
        ];
    }

    public function nonTransactionProvider()
    {
        return [
            ["/test_model10", "1"]
        ];
    }

}
