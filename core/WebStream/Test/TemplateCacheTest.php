<?php
namespace WebStream\Test;

use WebStream\Module\Utility;
use WebStream\Module\Logger;
use WebStream\Module\HttpClient;
use WebStream\Module\Cache;
use WebStream\Module\Container;
use WebStream\DI\ServiceLocator;
use WebStream\Annotation\Reader\AnnotationReader;
use WebStream\Test\DataProvider\TemplateCacheProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/TemplateCacheProvider.php';
require_once 'TestData/TestContainer/TestTemplateCacheContainer.php';

/**
 * TemplateCacheクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/11/10
 * @version 0.4
 */
class TemplateCacheTest extends TestBase
{
    use Utility, TestConstant, TemplateCacheProvider;

    public function setUp()
    {
        Logger::init($this->getLogConfigPath() . "/log.test.debug.ok.ini");
        parent::setUp();
    }

    /**
     * 正常系
     * @TemplateCacheのexpire属性で指定した値が取得できること
     * @test
     */
    public function okTemplateCacheExpire()
    {
        ServiceLocator::test();
        $container = ServiceLocator::getContainer();
        $templateCacheContainer = new \WebStream\Test\TestData\TestContainer\TestTemplateCacheContainer([
            "action" => "index1"
        ]);
        $container->router = $templateCacheContainer;
        $instance = new \WebStream\Test\TestData\TemplateCacheTest1($container);
        $reader = new AnnotationReader($instance, $container);
        $reader->read();
        $annotation = $reader->getInjectedAnnotationInfo();
        $this->assertEquals(100, $annotation["WebStream\Annotation\TemplateCache"][0]->expire);
    }

    /**
     * 正常系
     * @TemplateCacheのexpire属性に非常に大きな値が指定された場合、PHPの
     * Integerの最大値(PHP_INT_MAX)に丸められて設定されること
     * @test
     */
    public function okMaximumExpireConvert()
    {
        ServiceLocator::test();
        $container = ServiceLocator::getContainer();
        $templateCacheContainer = new \WebStream\Test\TestData\TestContainer\TestTemplateCacheContainer([
            "action" => "index2"
        ]);
        $container->router = $templateCacheContainer;
        $instance = new \WebStream\Test\TestData\TemplateCacheTest1($container);
        $reader = new AnnotationReader($instance, $container);
        $reader->read();
        $annotation = $reader->getInjectedAnnotationInfo();
        $this->assertEquals(PHP_INT_MAX, $annotation["WebStream\Annotation\TemplateCache"][0]->expire);
    }

    /**
     * 正常系
     * 有効期限が切れたらキャッシュファイルが削除されること
     * @test
     */
    public function okTemplateCacheRequest()
    {
        $http = new HttpClient();
        $path = $this->getDocumentRootURL() . "/test_template_cache/index1";
        $http->get($path);
        $dir = $this->getProjectRootPath() . $this->getCacheDir();
        $cache = new Cache($dir);
        $data = $cache->get("webstream-cache-test_template_cache-index1");
        $this->assertNotEquals($data, null);
        sleep(10);
        $data = $cache->get("webstream-cache-test_template_cache-index1");
        $this->assertEquals($data, null);
    }

    /**
     * 異常系
     * @TemplateCacheのexpire属性に不正な値が指定されていた場合、値が取得できないこと
     * @test
     * @expectedException WebStream\Exception\Extend\AnnotationException
     * @dataProvider invalidExpireProvider
     */
    public function ngTemplateCacheExpire($method)
    {
        ServiceLocator::test();
        $container = ServiceLocator::getContainer();
        $templateCacheContainer = new \WebStream\Test\TestData\TestContainer\TestTemplateCacheContainer([
            "action" => $method
        ]);
        $container->router = $templateCacheContainer;
        $instance = new \WebStream\Test\TestData\TemplateCacheTest1($container);
        $reader = new AnnotationReader($instance, $container);
        $reader->read();
        $exception = $reader->getException();

        $this->assertTrue(is_callable($exception));
        $exception();
    }
}
