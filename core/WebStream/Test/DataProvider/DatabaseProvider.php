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
            ["/test_model1", "honoka", "/test_model_prepare"],
            ["/test_model2", "honoka", "/test_model_prepare"],
            ["/test_model4", "honoka", "/test_model_prepare3"],
            ["/test_model5", "honoka", "/test_model_prepare3"],
            ["/test_model12", "kotori", "/test_model_prepare2"],
            ["/test_model13", "kotori", "/test_model_prepare2"]
        ];
    }

    public function commitProvider()
    {
        return [
            ["/test_model3", "kotori", "/test_model_clear"],
            ["/test_model14", "kotori", "/test_model_clear2"]
        ];
    }

    public function rollbackProvider()
    {
        return [
            ["/test_model7", "0", "/test_model_clear"],
            ["/test_model15", "0", "/test_model_clear2"]
        ];
    }

    public function nonTransactionProvider()
    {
        return [
            ["/test_model10", "1", "/test_model_clear"],
            ["/test_model17", "1", "/test_model_clear2"]
        ];
    }

    public function innserCallModelMethodProvider()
    {
        return [
            ["/test_model19", "honoka", "/test_model_prepare"],
            ["/test_model20", "kotori", "/test_model_prepare2"],
            ["/test_model21", "honoka", "/test_model_prepare3"]
        ];
    }

    public function okMultipleDatabaseAccessProvider()
    {
        return [
            ["/test_model22", "honokakotori", "/test_model_prepare", "/test_model_prepare2"]
        ];
    }

    public function okTransactionInControllerProvider()
    {
        return [
            ["/test_transaction1", "trans1trans2", "/test_transaction_clear"]
        ];
    }

    public function okTransactionRollbackInControllerProvider()
    {
        return [
            ["/test_transaction2", "", "/test_transaction_clear"]
        ];
    }

    public function okTransactionRollbackInModelProvider()
    {
        return [
            ["/test_transaction3", "", "/test_transaction_clear"]
        ];
    }

    public function useUndefinedQueryXmlFileProvider()
    {
        return [
            ["/test_model6", "\WebStream\Test\TestData\Sample\App\Controller\TestMysqlController#model5"],
            ["/test_model16", "\WebStream\Test\TestData\Sample\App\Controller\TestPostgresController#model5"],
            ["/test_model18", "\WebStream\Test\TestData\Sample\App\Controller\TestSqliteController#model3"]
        ];
    }

}
