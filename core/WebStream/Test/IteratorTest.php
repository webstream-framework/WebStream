<?php
namespace WebStream\Test;

use WebStream\Module\HttpClient;
use WebStream\Module\Logger;
use WebStream\Test\DataProvider\IteratorProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/IteratorProvider.php';

/**
 * Resultクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2014/07/01
 * @version 0.4
 */
class IteratorTest extends TestBase
{
    use IteratorProvider, TestConstant;

    public function setUp()
    {
        parent::setUp();
        Logger::init($this->getLogConfigPath() . "/log.test.debug.ok.ini");
    }

    /**
     * 正常系
     * Resultクラスのイテレータ機能が意図どおりに動作すること
     * @test
     * @dataProvider iteratorProvider
     */
    public function okIterator($path, $response)
    {
        $http = new HttpClient();
        $result = $http->get($this->getDocumentRootURL() . $path);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($response, $result);
    }
}
