<?php
namespace WebStream\Test;

use WebStream\Module\Logger;
use WebStream\Module\HttpClient;
use WebStream\Test\DataProvider\RouterProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/RouterProvider.php';

/**
 * Routerクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/21
 * @version 0.4
 */
class RouterTest extends TestBase
{
    use RouterProvider, TestConstant;

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
     * プレースホルダなしのパスに対して正常にアクセスできること
     * @test
     * @dataProvider resolvePathWithoutPlaceHolderProvider
     */
    public function okResolvePathWithoutPlaceHolder($path, $ca, $responseText)
    {
        $url = $this->getDocumentRootURL() . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, $responseText);
    }

    /**
     * 正常系
     * プレースホルダありのパスに対して正常にアクセスできること
     * @test
     * @dataProvider resolvePathWithPlaceHolderProvider
     */
    public function okResolvePathWithPlaceHolder($path, $ca, $param)
    {
        $path = preg_replace('/:[a-zA-Z0-9]+/', $param, $path, 1);
        $url = $this->getDocumentRootURL() . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, $param);
    }

    /**
     * 正常系
     * アクション名がキャメルケースの場合、正常に実行出来ること
     * @test
     * @dataProvider resolveCamelActionProvider
     */
    public function okResolveCamelAction($path, $str)
    {
        $url = $this->getDocumentRootURL() . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, $str);
    }

    /**
     * 正常系
     * 拡張子指定のようなプレースホルダ定義(/feed.:format)にアクセスできること
     * @test
     * @dataProvider resolveWithPlaceHolderFormatProvider
     */
    public function okResolveWithPlaceHolderFormat($path)
    {
        $url = $this->getDocumentRootURL() . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, "rss");
    }

    /**
     * 正常系
     * コントローラ名の指定にアンダースコアが含まれている場合、
     * キャメルケースに変換されたコントローラクラスにアクセスできること
     * @test
     * @dataProvider snakeControllerProvider
     */
    public function okSnakeController($path, $str)
    {
        $url = $this->getDocumentRootURL() . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, $str);
    }

    /**
     * 正常系
     * プレースホルダにURLエンコードされた文字列が指定された場合、
     * 正常にアクセスでき、文字化けしないこと
     * @test
     * @dataProvider uriWithEncodedStringProvider
     */
    public function okUriWithEncodedString($path, $str)
    {
        $url = $this->getDocumentRootURL() . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, $str);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "200");
    }

    /**
     * 正常系
     * ルーティング定義の前半部分一致が起きる場合でも正常にパスの解決ができること
     * @test
     * @dataProvider resolveSimilarUrlProvider
     */
    public function okResolveSimilarUrl($path, $str)
    {
        $url = $this->getDocumentRootURL() . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, $str);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "200");
    }

    /**
     * 正常系
     * 指定ディレクトリに配置した静的ファイルを読み込めること
     * @test
     * @dataProvider readStaticFileProvider
     */
    public function okReadStaticFile($path, $contentType)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $path;
        $http->get($url);
        $header = $http->getResponseHeader();
        $responseContentType = null;
        if (preg_match("/^Content-Type:\s(.+?);/", $header[13], $matches)) {
            $responseContentType = $matches[1];
        }

        $this->assertEquals($contentType, $responseContentType);
    }

    /**
     * 正常系
     * 指定ディレクトリに配置した静的ファイルをダウンロードできること
     * @test
     * @dataProvider downloadStaticFile
     */
    public function okDownloadStaticFile($path, $contentType)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $path;
        $http->get($url);
        $header = $http->getResponseHeader();
        $responseContentType = null;
        if (preg_match("/^Content-Type:\s(.+?);/", $header[15], $matches)) {
            $responseContentType = $matches[1];
        }

        $this->assertEquals($contentType, $responseContentType);
        $this->assertRegExp("/^Content-Disposition: attachement;/", $header[10]);
    }

    /**
     * 正常系
     * ユーザ指定ディレクトリに配置した静的ファイルを表示できること
     * @test
     * @dataProvider readCustomStaticFile
     */
    public function okReadCustomStaticFile($path, $contentType)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $path;
        $http->get($url);
        $header = $http->getResponseHeader();
        $responseContentType = null;
        if (preg_match("/^Content-Type:\s(.+?);/", $header[13], $matches)) {
            $responseContentType = $matches[1];
        }

        $this->assertEquals($contentType, $responseContentType);
    }

    /**
     * 正常系
     * 開発者が定義したクラスを各階層で呼べること
     * @test
     * @dataProvider customDirProvider
     */
    public function okCustomDir($path, $response)
    {
        $http = new HttpClient();
        $result = $http->get($this->getDocumentRootURL() . $path);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($response, $result);
    }

    /**
     * 異常系
     * 存在しないコントローラまたはアクションが指定された場合、500エラーになること
     * @test
     * @dataProvider resolveUnknownProvider
     */
    public function ngResolveUnknown($path, $ca)
    {
        $url = $this->getDocumentRootURL() . $path;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "500");
    }

    /**
     * 異常系
     * コントローラ名の指定にアンダースコアの連続が含まれている場合、
     * 500エラーになること
     * @test
     * @dataProvider multipleSnakeControllerProvider
     */
    public function ngMultipleSnakeController($path)
    {
        $url = $this->getDocumentRootURL() . $path;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "500");
    }
}
