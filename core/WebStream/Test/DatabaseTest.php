<?php
namespace WebStream\Test;

use WebStream\Module\Logger;
use WebStream\Module\HttpClient;
use WebStream\Test\DataProvider\DatabaseProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/DatabaseProvider.php';

/**
 * Database関連のテストクラス
 * @author Ryuichi TANAKA.
 * @since 2014/01/18
 * @version 0.4
 */
class DatabaseTest extends TestBase
{
    use DatabaseProvider, TestConstant;

    public function setUp()
    {
        Logger::init($this->getLogConfigPath() . "/log.test.debug.ok.ini");
        parent::setUp();
    }

    public function tearDown()
    {
    }

    /**
     * 正常系
     * SELECT文を直接実行できること
     * @test
     * @dataProvider selectProvider
     */
    public function okSelect($path, $response, $preparePath)
    {
        $http = new HttpClient();
        if ($preparePath !== null) {
            $url = $this->getDocumentRootURL() . $preparePath;
            $http->get($url);
        }
        $url = $this->getDocumentRootURL() . $path;
        $html = $http->get($url);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($html, $response);
    }

    /**
     * 正常系
     * Commitを実行したとき、コミットされること(MySQL/PostgreSQL)
     * @test
     * @dataProvider commitProvider
     */
    public function okCommit($path, $response, $pareparePath)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $pareparePath;
        $http->get($url);
        $url = $this->getDocumentRootURL() . $path;
        $html = $http->get($url);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($html, $response);
    }

    /**
     * 正常系
     * Rollbackを実行したとき、コミットされないこと(MySQL/PostgreSQL)
     * @test
     * @dataProvider commitProvider
     */
    public function okRollback($path, $response, $pareparePath)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $pareparePath;
        $http->get($url);
        $url = $this->getDocumentRootURL() . $path;
        $html = $http->get($url);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($html, $response);
    }

    /**
     * 正常系
     * 明示的なbeginTransactionなしで更新処理を実行したとき、自動コミットされること(MySQL/PostgreSQL)
     * @test
     * @dataProvider nonTransactionProvider
     */
    public function okNonTransaction($path, $response, $preparePath)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $preparePath;
        $http->get($url);
        $url = $this->getDocumentRootURL() . $path;
        $html = $http->get($url);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($html, $response);
    }

    /**
     * 正常系
     * Modelクラス内でModelメソッドを呼んだ時もアノテーションからQuery情報を取得し実行できること(MySQL/PostgreSQL)
     * @test
     * @dataProvider innserCallModelMethodProvider
     */
    public function okInnerCallModelMethod($path, $response, $preparePath)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $preparePath;
        $http->get($url);
        $url = $this->getDocumentRootURL() . $path;
        $html = $http->get($url);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($html, $response);
    }

    /**
     * 正常系
     * 子クラスと親クラスで別DBに接続している場合、それぞれのDBから値を取得できること
     * @test
     * @dataProvider multipleDatabaseAccessProvider
     */
    public function okMultipleDatabaseAccess($path, $response, $preparePath1, $preparePath2)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $preparePath1;
        $http->get($url);
        $url = $this->getDocumentRootURL() . $preparePath2;
        $http->get($url);
        $url = $this->getDocumentRootURL() . $path;
        $html = $http->get($url);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($html, $response);
    }

    /**
     * 正常系
     * Controllerクラス内で明示的にトランザクションスコープを指定した場合、すべて正常にコミットされること
     * @test
     * @dataProvider transactionInControllerProvider
     */
    public function okTransactionInController($path, $response, $preparePath)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $preparePath;
        $http->get($url);
        $url = $this->getDocumentRootURL() . $path;
        $html = $http->get($url);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($html, $response);
    }

    /**
     * 正常系
     * Controllerクラス内で明示的にトランザクションスコープを指定した場合、すべて正常にロールバックされること
     * @test
     * @dataProvider transactionRollbackInControllerProvider
     */
    public function okTransactionRollbackInController($path, $response, $preparePath)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $preparePath;
        $http->get($url);
        $url = $this->getDocumentRootURL() . $path;
        $html = $http->get($url);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($html, $response);
    }

    /**
     * 正常系
     * Modelクラス内で明示的にトランザクションスコープを指定した場合、すべて正常にロールバックされること
     * @test
     * @dataProvider transactionRollbackInModelProvider
     */
    public function okTransactionRollbackInModel($path, $response, $preparePath)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $preparePath;
        $http->get($url);
        $url = $this->getDocumentRootURL() . $path;
        $html = $http->get($url);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($html, $response);
    }

    /**
     * 正常系
     * 設定ファイルに.yml|.yamlを指定した時、正常にデータが取得できること
     * @test
     * @dataProvider yamlConfigProvider
     */
    public function okYamlConfig($path, $response, $preparePath)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $preparePath;
        $http->get($url);
        $url = $this->getDocumentRootURL() . $path;
        $html = $http->get($url);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($html, $response);
    }

    /**
     * 異常系
     * 不明なDatabaseDriverを指定した場合、例外が発生すること
     * @test
     */
    public function ngUseUndefinedDriver()
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . "/test_model8";
        $html = $http->get($url);
        $this->assertEquals($html, "\WebStream\Test\TestData\Sample\App\Controller\TestDatabaseError1Controller#model1");
    }

    /**
     * 異常系
     * 不明なデータベース設定ファイルを指定した場合、例外が発生すること
     * @test
     */
    public function ngUseUndefinedConfig()
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . "/test_model9";
        $html = $http->get($url);
        $this->assertEquals($html, "\WebStream\Test\TestData\Sample\App\Controller\TestDatabaseError2Controller#model1");
    }

    /**
     * 異常系
     * QueryXMLファイルパスが存在しない場合、例外が発生すること
     * @test
     * @dataProvider useUndefinedQueryXmlFileProvider
     */
    public function ngUseUndefinedQueryXmlFile($path, $classpath)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $path;
        $html = $http->get($url);
        $this->assertEquals($html, $classpath);
    }

    /**
     * 異常系
     * QueryXMLファイル内のmapperタグの名前空間とModelクラスの名前空間が一致しない場合、例外が発生すること
     * @test
     */
    public function ngInvalidNamespaceQueryXmlFile()
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . "/test_model11";
        $html = $http->get($url);
        $this->assertEquals($html, "\WebStream\Test\TestData\Sample\App\Controller\TestDatabaseError3Controller#model1");
    }
}