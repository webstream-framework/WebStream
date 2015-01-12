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

    public function multipleDatabaseAccessProvider()
    {
        return [
            ["/test_model22", "honokakotori", "/test_model_prepare", "/test_model_prepare2"]
        ];
    }

    public function transactionInControllerProvider()
    {
        return [
            ["/test_transaction1", "trans1trans2", "/test_transaction_clear"]
        ];
    }

    public function transactionRollbackInControllerProvider()
    {
        return [
            ["/test_transaction2", "", "/test_transaction_clear"]
        ];
    }

    public function transactionRollbackInModelProvider()
    {
        return [
            ["/test_transaction3", "", "/test_transaction_clear"]
        ];
    }

    public function yamlConfigProvider()
    {
        return [
            ["/test_model23", "honoka", "/test_model_prepare4"],
            ["/test_model24", "honoka", "/test_model_prepare4"]
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

    public function entityMappingProvider()
    {
        return [
            ["/test_model25", "honoka", "test_model_prepare"],
            ["/test_model26", "kotori", "test_model_prepare2"],
            ["/test_model27", "honoka", "test_model_prepare3"],
            ["/test_model28", "honokakotoriumichang", "test_model_prepare5"],
            ["/test_model29", "honokakotoriumichang", "test_model_prepare6"],
            ["/test_model30", "honokakotoriumichang", "test_model_prepare7"]
        ];
    }

    public function entityMappingMultipleTableProvider()
    {
        return [
            ["/test_model31", "honokahonokakotoriumichang", "test_model_prepare", "test_model_prepare5"],
            ["/test_model32", "kotorihonokakotoriumichang", "test_model_prepare", "test_model_prepare6"],
            ["/test_model33", "honokahonokakotoriumichang", "test_model_prepare", "test_model_prepare7"]
        ];
    }

    public function entityMappingAliasProvider()
    {
        return [
            ["/test_model34", "test_model_prepare", "test_model_prepare5"],
            ["/test_model35", "test_model_prepare", "test_model_prepare6"],
            ["/test_model36", "test_model_prepare", "test_model_prepare7"]
        ];
    }

    public function entityMappingTypeProvider()
    {
        return [
            ["/test_model37", "integerstringobjectstringobjectdoubleinteger", "test_model_prepare8"],
            ["/test_model38", "integerstringobjectstringobjectdoubleinteger", "test_model_prepare9"],
            ["/test_model39", "integerstringobjectstringobjectdoubleinteger", "test_model_prepare10"]
        ];
    }

}
