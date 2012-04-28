<?php
/**
 * Routerクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/08/25
 */
require_once 'UnitTestBase.php';

class RouterTest extends UnitTestBase {
    private $route;
    
    public function setUp() {
        parent::setUp();
    }
    
    public function tearDown() {
        $log_path = Utility::getRoot() . $this->testdata_dir . "/log/stream.log";
        $handle = fopen($log_path, "w+");
        fclose($handle);
    }
    
    /**
     * 正常系
     * ルートパス(/)にアクセスできること
     * @dataProvider resolveRootPathProvider
     */
    public function testOkResolveRootPath($path, $ca) {
        $url = $this->root_url . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, "test1");
    }
    
    /**
     * 正常系
     * プレースホルダなしのパス(/top)にアクセスできること
     * @dataProvider resolveWithoutPlaceHolderProvider
     */
    public function testOkResolveWithoutPlaceHolder($path, $ca) {
        $url = $this->root_url . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, "test2");
    }
    
    /**
     * 正常系
     * プレースホルダなしのパス(/top)にアクセスできること
     * @dataProvider resolveWithPlaceHolderProvider
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
     * @dataProvider resolveCamelActionProvider
     */
    public function testOkResolveCamelAction($path, $str) {
        $url = $this->root_url . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, $str);
    }
    
    /**
     * 正常系
     * 拡張子指定のようなプレースホルダ定義(/feed.:format)にアクセスできること
     * @dataProvider resolveWithPlaceHolderFormatProvider
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
     * @dataProvider snakeControllerProvider
     */
    public function testOkSnakeController($path, $str) {
        $url = $this->root_url . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, $str);
    }
    
    /**
     * 正常系
     * プレースホルダにURLエンコードされた文字列が指定された場合、
     * 正常にアクセスでき、文字化けしないこと
     * @dataProvider uriWithEncodedStringProvider
     */
    public function testOkUriWithEncodedString($path, $str) {
        $url = $this->root_url . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, $str);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "200");
    }
    
    /**
     * 正常系
     * ルーティング定義の前半部分一致が起きる場合でも正常にパスの解決ができること
     * @dataProvider resolveSimilarUrlProvider
     */
    public function testOkResolveSimilarUrl($path, $str) {
        $url = $this->root_url . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, $str);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "200");
    }
    
    /**
     * 正常系
     * Serviceクラスが存在しない場合、直接Modelクラスを呼び出せること
     * @dataProvider noServiceClass
     */
    public function testOkNoServiceClass($path, $str) {
        $url = $this->root_url . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, $str);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "200");
    }
    
    /**
     * 正常系
     * Serviceクラスに該当するメソッドがない場合、Modelクラスのメソッドに移譲できること
     * @dataProvider noServiceMethod
     */
    public function testOkNoServiceMethod($path, $str) {
        $url = $this->root_url . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, $str);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "200");
    }
    
    /**
     * 正常系
     * Serviceクラスに該当するメソッドがなく、Modelクラスのメソッドに以上する場合、
     * 引数を正常に渡すことができること
     * @dataProvider sendParamFromControllerToModelProvider
     */
    public function testOkSendParamFromControllerToModel($path, $str) {
        $url = $this->root_url . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, $str);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "200");
    }
    
    /**
     * 正常系
     * テンプレートファイルを使ったViewの描画が正常に実行出来ること
     * @dataProvider renderTemplateProvider
     */
    public function testOkRenderTemplate($path, $str) {
        $url = $this->root_url . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, $str);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "200");
    }
    
    /**
     * 正常系
     * レイアウトテンプレートファイルを使ったViewの描画が正常に実行出来ること
     * @dataProvider renderLayoutTemplateProvider
     */
    public function testOkRenderLayoutTemplate($path, $str) {
        $url = $this->root_url . $path;
        $response = file_get_contents($url);
        $this->assertEquals($response, $str);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "200");
    }
    
    /**
     * 正常系
     * 公開している各種リソースファイルにアクセスできること
     * @dataProvider getResourceProvider
     */
    public function testOkGetResource($path, $mime) {
        $url = $this->root_url . $path;
        $response = file_get_contents($url);
        $response_mime = null;
        foreach ($http_response_header as $header) {
            if (preg_match('/^Content-Type:\s(.*);/', $header, $matches)) {
                $response_mime = $matches[1];
                break;
            }
        }
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "200");
        $this->assertEquals($mime, $response_mime);
    }
    
    /**
     * 正常系
     * 公開している各種ファイルにアクセスできること
     * @dataProvider getFileProvider
     */
    public function testOkGetFile($path, $mime) {
        $url = $this->root_url . $path;
        $response = file_get_contents($url);
        $response_mime = null;
        foreach ($http_response_header as $header) {
            if (preg_match('/^Content-Type:\s(.*);/', $header, $matches)) {
                $response_mime = $matches[1];
                break;
            }
        }
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "200");
        $this->assertEquals($mime, $response_mime);
    }

    /**
     * 正常系
     * render系メソッドに正常にアクセスできること
     * @dataProvider renderMethodProvider
     */
    public function testOkRenderMethod($path, $mime) {
        $url = $this->root_url . $path;
        $response = file_get_contents($url);
        $response_mime = null;
        foreach ($http_response_header as $header) {
            if (preg_match('/^Content-Type:\s(.*);/', $header, $matches)) {
                $response_mime = $matches[1];
                break;
            }
        }
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "200");
        $this->assertEquals($mime, $response_mime);
    }
    
    /**
     * 正常系
     * GETリクエストが正常に実行出来ること
     * @dataProvider getRequestProvider
     */
    public function testOkGetRequest($path, $key, $value) {
        $url = $this->root_url . $path . "?${key}=${value}";
        $response = file_get_contents($url);
        $this->assertEquals($response, $value);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "200");
    }
    
    /**
     * 正常系
     * POSTリクエストが正常に実行出来ること
     * @dataProvider postRequestProvider
     */
    public function testOkPostRequest($path, $key, $value) {
        $url = $url = $this->root_url . $path;
        $http = new HttpAgent();
        $response = $http->post($url, array($key => $value));
        $this->assertEquals($response, $value);
        $this->assertEquals($http->getStatusCode(), "200");
    }
    
    /**
     * 正常系
     * SESSIONに値をセット出来ること
     * @dataProvider setSessionProvider
     */
    public function testOkSetSession($path, $key, $value, $path2) {
        $http = new HttpAgent();
        $url = $this->root_url . $path;
        $session_id = $http->get($url);
        $cookie = "Cookie: PHPSESSID=${session_id}";
        $url = $this->root_url . $path2;
        $response = $http->get($url, null, array($cookie));
        $this->assertEquals($response, $value);
        $this->assertEquals($http->getStatusCode(), "200");
    }
    
    /**
     * 正常系
     * 意図したステータスコードを返却できること
     * @dataProvider retrurnStatusCodeProvider
     */
    public function testOkReturnStatusCode($path, $status_code) {
        $http = new HttpAgent();
        $url = $this->root_url . $path;
        $response = $http->get($url);
        $this->assertEquals($http->getStatusCode(), $status_code);
    }
    
    /**
     * 正常系
     * ヘルパメソッドを利用しHTMLを取得できること
     * @dataProvider useHelperProvider
     */
    public function testOkUseHelper($path, $html) {
        $http = new HttpAgent();
        $url = $this->root_url . $path;
        $response = trim($http->get($url));
        $this->assertEquals($response, $html);
    }
    
    /**
     * 正常系
     * ヘルパ名がスネークケースでもキャメルケースでも、正常に結果を取得できること
     * @dataProvider helperFunctionNameProvider
     */
    public function testOkHelperFunctionName($path, $html) {
        $http = new HttpAgent();
        $url = $this->root_url . $path;
        $response = trim($http->get($url));
        $this->assertEquals($response, $html);
    }
    
    /**
     * 異常系
     * 存在しないコントローラまたはアクションが指定された場合、500エラーになること
     * @dataProvider resolveUnknownProvider
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
     * @dataProvider resolveNotDefinePathProvider
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
     * @dataProvider resolveRenderProvider
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
     * @dataProvider resolveLayoutProvider
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
     * @dataProvider resolveLayoutProvider
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
     * @dataProvider resolveLoadProvider
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
     * @dataProvider resolveBeforeProvider
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
     * @dataProvider resolveAfterProvider
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
     * @dataProvider resolveCamelControllerProvider
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
     * @dataProvider resolveInvalidPathProvider
     * @expectedException RouterException
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
     * @dataProvider prohibitPathProvider
     * @expectedException RouterException
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
     * @dataProvider multipleSnakeControllerProvider
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
     * @dataProvider uriWithoutUtf8EncodedStringProvider
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
     * @dataProvider withNullByteProvider
     */
    public function testNgUriWithNullByte($path) {
        $url = $this->root_url . $path;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "404");
    }
    
    /**
     * 異常系
     * ControllerからService、Modelを呼び出したとき、
     * Serviceクラス、Modelクラスともに存在しない場合、500エラーとなり、
     * Serviceクラス名 and Modelクラス名 is not found.とログ出力されること
     * @dataProvider noServiceNoModel
     */
    public function testNgNoServiceNoModel($path, $error_msg) {
        $url = $this->root_url . $path;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "500");
        $line_tail = $this->logTail($this->config_path_log . "log.test.info.ok.ini");
        
        if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},\d{1,2}\]\s\[(.+?)\]\s(.*?)\s-.*$/',
                       $line_tail, $matches)) {
            $target = array("ERROR", $error_msg);
            $result = array($matches[1], $matches[2]);
            $this->assertEquals($target, $result);           
        }
    }
    
    /**
     * 異常系
     * ControllerからService、Modelを呼び出したとき、
     * Serviceクラス、Modelクラスともに存在するが、メソッドが存在しない場合、500エラーになり、
     * Serviceクラス名#メソッド名 and Modelクラス名#メソッド名 is not defined.とログ出力されること
     * @dataProvider existServiceExistModelNoMethod
     */
    public function testExistServiceExistModelNoMethod($path, $error_msg) {
        $url = $this->root_url . $path;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "500");
        $line_tail = $this->logTail($this->config_path_log . "log.test.info.ok.ini");
        
        if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},\d{1,2}\]\s\[(.+?)\]\s(.*?)\s-.*$/',
                       $line_tail, $matches)) {
            $target = array("ERROR", $error_msg);
            $result = array($matches[1], $matches[2]);
            $this->assertEquals($target, $result);           
        }
    }
    
    /**
     * 異常系
     * ControllerからService、Modelを呼び出したとき、
     * Serviceクラスが存在せず、Modelクラスが存在するが、メソッドが存在しない場合、500エラーになり
     * Modelクラス名#メソッド名 is not defined.とログ出力されること
     * @dataProvider noServiceExistModelNoMethod
     */
    public function testNgNoServiceExistModelNoMethod($path, $error_msg) {
        $url = $this->root_url . $path;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "500");
        $line_tail = $this->logTail($this->config_path_log . "log.test.info.ok.ini");
        
        if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},\d{1,2}\]\s\[(.+?)\]\s(.*?)\s-.*$/',
                       $line_tail, $matches)) {
            $target = array("ERROR", $error_msg);
            $result = array($matches[1], $matches[2]);
            $this->assertEquals($target, $result);           
        }
    }
    
    /**
     * 異常系
     * ControllerからService、Modelを呼び出したとき、
     * Serviceクラスが存在し、Modelクラスが存在しないが、メソッドが存在しない場合、500エラーになり、
     * Serviceクラス名#メソッド名 is not defined.とログ出力されること
     * @dataProvider existServiceNoModelNoMethod
     */
    public function testNgExistServiceNoModelNoMethod($path, $error_msg) {
        $url = $this->root_url . $path;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "500");
        $line_tail = $this->logTail($this->config_path_log . "log.test.info.ok.ini");
        
        if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},\d{1,2}\]\s\[(.+?)\]\s(.*?)\s-.*$/',
                       $line_tail, $matches)) {
            $target = array("ERROR", $error_msg);
            $result = array($matches[1], $matches[2]);
            $this->assertEquals($target, $result);           
        }
    }
    
    /**
     * 異常系
     * 存在しないrenderメソッドにアクセスしたとき、500エラーとなり、
     * Controllerクラス名#メソッド名 is not defined.とログ出力されること
     * @dataProvider notFoundRenderMethodProvider
     */
    public function testNgNotFoundRenderMethodProvider($path, $error_msg) {
        $url = $this->root_url . $path;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "500");
        $line_tail = $this->logHead($this->config_path_log . "log.test.info.ok.ini");

        if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},\d{1,2}\]\s\[(.+?)\]\s(.*?)$/',
                       $line_tail, $matches)) {
            $target = array("ERROR", $error_msg);
            $result = array($matches[1], $matches[2]);
            $this->assertEquals($target, $result);           
        }
    }
    
    /**
     * 異常系
     * ヘルパメソッドが存在しない場合(クラス自体が存在しない場合含む)、500エラーになり、
     * Helperクラス名#メソッド名 is not defined.とログ出力されること
     * @dataProvider notFoundHelperMethodProvider
     */
    public function testNgNotFoundHelperMethod($path, $error_msg) {
        $url = $this->root_url . $path;
        @file_get_contents($url);
        list($version, $status_code, $msg) = explode(' ', $http_response_header[0], 3);
        $this->assertEquals($status_code, "500");
        $line_tail = $this->logHead($this->config_path_log . "log.test.info.ok.ini");

        if (preg_match('/^\[\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2},\d{1,2}\]\s\[(.+?)\]\s(.*?)$/',
                       $line_tail, $matches)) {
            $target = array("ERROR", $error_msg);
            $result = array($matches[1], $matches[2]);
            $this->assertEquals($target, $result);           
        }
    }
}
