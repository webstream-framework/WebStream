<?php
/**
 * Routerクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/08/25
 */
require_once '../../core/AutoImport.php';
import("core/test/UnitTestBase");

class RouterTest extends UnitTestBase {
    private $route;
    
    /**
     * 正常系
     * ルートパス(/)にアクセスできること
     * @dataProvider testOkResolveRootPathProvider
     */
    public function testOkResolveRootPath($path, $ca) {
        $url = $this->root_url . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, "test1");
    }
    
    /**
     * 正常系
     * プレースホルダなしのパス(/top)にアクセスできること
     * @dataProvider testOkResolveWithoutPlaceHolderProvider
     */
    public function testOkResolveWithoutPlaceHolder($path, $ca) {
        $url = $this->root_url . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, "test2");
    }
    
    /**
     * 正常系
     * プレースホルダなしのパス(/top)にアクセスできること
     * @dataProvider testOkResolveWithPlaceHolderProvider
     */
    public function testOkResolveWithPlaceHolder($path, $ca, $param) {
        $path = preg_replace('/:[a-zA-Z0-9]+/', $param,  $path, 1);
        $url = $this->root_url . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, "test3");
    }
    
    /**
     * 正常系
     * アクション名がキャメルケースの場合、正常に実行出来ること
     * @dataProvider testOkResolveCamelActionProvider
     */
    public function testOkResolveCamelAction($path) {
        $url = $this->root_url . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, "testAction");
    }
    
    /**
     * 正常系
     * 拡張子指定のようなプレースホルダ定義(/feed.:format)にアクセスできること
     * @dataProvider testOkResolveWithPlaceHolderFormatProvider
     */
    public function testOkResolveWithPlaceHolderFormat($path) {
        $url = $this->root_url . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, "rss");
    }
    
    /**
     * 正常系
     * コントローラ名の指定にアンダースコアが含まれている場合、キャメルケースに変換された
     * コントローラクラスにアクセスできること
     * @dataProvider testOkSnakeControllerProvider
     */
    public function testOkSnakeController($path) {
        $url = $this->root_url . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, "snake");
    }
    
    /**
     * 正常系
     * プレースホルダにURLエンコードされた文字列が指定された場合、
     * 正常にアクセスでき、文字化けしないこと
     * @dataProvider testOkUriWithEncodedStringProvider
     */
    public function testOkUriWithEncodedString($path, $str) {
        $url = $this->root_url . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, $str);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "200");
    }
    
    /**
     * 異常系
     * 存在しないコントローラまたはアクションが指定された場合、500エラーになること
     * @dataProvider testNgResolveUnknownProvider
     */
    public function testNgResolveUnknown($path, $ca) {
        $url = $this->root_url . $path;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "500");
    }
    
    /**
     * 異常系
     * routes.phpに未定義のパスが指定された場合、404エラーになること
     * @dataProvider testNgResolveNotDefinePathProvider
     */
    public function testNgResolveNotDefinePath($path, $ca) {
        $url = $this->root_url . $path;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "404");
    }
    
    /**
     * 異常系
     * renderメソッドはアクションに指定した場合、500エラーになること
     * @dataProvider testNgResolveRenderProvider
     */
    public function testNgResolveRender($path) {
        $url = $this->root_url . $path;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "500");
    }
    
    /**
     * 異常系
     * layoutメソッドはアクションに指定した場合、500エラーになること
     * @dataProvider testNgResolveLayoutProvider
     */
    public function testNgResolveLayout($path) {
        $url = $this->root_url . $path;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "500");
    }
    
    /**
     * 異常系
     * redirectメソッドはアクションに指定した場合、500エラーになること
     * @dataProvider testNgResolveLayoutProvider
     */
    public function testNgResolveRedirect($path) {
        $url = $this->root_url . $path;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "500");
    }
    
    /**
     * 異常系
     * loadメソッドはアクションに指定した場合、500エラーになること
     * @dataProvider testNgResolveLoadProvider
     */
    public function testNgResolveLoad($path) {
        $url = $this->root_url . $path;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "500");
    }
    
    /**
     * 異常系
     * beforeメソッドはアクションに指定した場合、500エラーになること
     * @dataProvider testNgResolveBeforeProvider
     */
    public function testNgResolveBefore($path) {
        $url = $this->root_url . $path;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "500");
    }
    
    /**
     * 異常系
     * afterメソッドはアクションに指定した場合、500エラーになること
     * @dataProvider testNgResolveAfterProvider
     */
    public function testNgResolveAfter($path) {
        $url = $this->root_url . $path;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "500");
    }
    
    /**
     * 異常系
     * コントローラ名に半角小文字英字、数字以外が含まれている場合、500エラーになること
     * @dataProvider testNgResolveCamelControllerProvider
     */
    public function testNgResolveCamelController($path, $ca) {
        $url = $this->root_url . $path;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "500");
    }
    
    /**
     * 異常系
     * ルーティングルールが指定された文字以外で構成されていた場合、例外が発生すること
     * @dataProvider testNgResolveInvalidPathProvider
     * @expectedException Exception
     */
    public function testNgResolveInvalidPath($path) {
        Router::setRule(array(
            $path => "dummy#dummy",
        ));
        new Router();
    }
    
    /**
     * 異常系
     * ルーティングルールに静的ファイルへのパスが指定された場合、例外が発生すること
     * @dataProvider testNgProhibitPathProvider
     * @expectedException Exception
     */
    public function testNgProhibitPath($path) {
        Router::setRule(array(
            $path => "dummy#dummy",
        ));
        new Router();
    }
    
    /**
     * 異常系
     * コントローラ名の指定にアンダースコアの連続が含まれている場合、500エラーになること
     * @dataProvider testNgMultipleSnakeControllerProvider
     */
    public function testNgMultipleSnakeController($path) {
        $url = $this->root_url . $path;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "500");
    }

    /**
     * 異常系
     * プレースホルダにURLエンコードされていない文字列またはUTF-8以外でエンコードした場合、
     * 200が返ってくるが、文字化けしていること
     * @dataProvider testNgUriWithoutUtf8EncodedStringProvider
     */
    public function testNgUriWithoutUtf8EncodedString($path, $str) {
        $url = $this->root_url . $path;
        $response = file_get_contents($url);
        $this->assertNotEquals($response, $str);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "200");
    }
    
    /**
     * 異常系
     * NULLバイトが含まれるURLが指定された場合、404エラーになること
     * @dataProvider testNgWithNullByteProvider
     */
    public function testNgUriWithNullByte($path) {
        $url = $this->root_url . $path;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "404");
    }
}
