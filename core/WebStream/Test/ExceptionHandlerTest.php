<?php
namespace WebStream\Test;

use WebStream\Module\Logger;
use WebStream\Module\HttpClient;
use WebStream\Test\DataProvider\ExceptionHandlerProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/ExceptionHandlerProvider.php';

/**
 * ExceptionHandlerクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/11/26
 * @version 0.4
 */
class ExceptionHandlerTest extends TestBase
{
    use TestConstant, ExceptionHandlerProvider;

    public function setUp()
    {
        parent::setUp();
        Logger::init($this->getLogConfigPath() . "/log.test.debug.ok.ini");
    }

    /**
     * 正常系
     * 指定した例外をメソッドで補足できること
     * @test
     * @dataProvider successErrorHandlingProvider
     */
    public function okSuccessErrorHandling($path, $handleMessage)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $path;
        $message = $http->get($url);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($message, $handleMessage);
    }

    /**
     * 正常系
     * 指定した例外を複数メソッドで補足できること
     * @test
     */
    public function okMultipleErrorHandling()
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . '/multiple_exception_handler11';
        $message = $http->get($url);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($message, "12");
    }

    /**
     * 異常系
     * ハンドリング不可例外は500を返却すること
     * @test
     * @dataProvider failureErrorHandlingProvider
     */
    public function ngFailureErrorHandling($path)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $path;
        $message = $http->get($url);
        $this->assertEquals($http->getStatusCode(), 500);
    }
}
