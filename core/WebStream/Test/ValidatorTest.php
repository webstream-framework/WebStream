<?php
namespace WebStream\Test;

// use WebStream\Delegate\Validator;
// use WebStream\DI\ServiceLocator;
use WebStream\Module\Logger;
use WebStream\Module\HttpClient;
use WebStream\Test\DataProvider\ValidatorProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/ValidatorProvider.php';

/**
 * Validatorテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/10/19
 * @version 0.4
 */
class ValidatorTest extends TestBase
{
    use ValidatorProvider, TestConstant;

    private $container;
    private $classLoader;

    public function setUp()
    {
        parent::setUp();
        Logger::init($this->getLogConfigPath() . "/log.test.debug.ok.ini");
    }

    public function tearDown()
    {
    }

    /**
     * 正常系
     * バリデーション定義ファイルにエラーがない場合、例外が発生しないこと
     * @test
     * @dataProvider validatorProvider
     */
    public function okValidatorInitialize($path, $params)
    {
        $http = new HttpClient();
        $url = $this->getDocumentRootURL() . $path;
        $html = $http->get($url, $params);
        $this->assertEquals($http->getStatusCode(), 200);
        $this->assertEquals($html, current($params));
    }
}
