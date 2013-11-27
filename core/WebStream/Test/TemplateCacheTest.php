<?php
namespace WebStream\Test;

use WebStream\Module\Utility;
use WebStream\Module\Logger;
use WebStream\Module\HttpClient;
use WebStream\Module\Cache;
use WebStream\Annotation\TemplateCacheReader;
use WebStream\Test\DataProvider\TemplateCacheProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/TemplateCacheProvider.php';

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
        parent::setUp();
        Logger::init($this->getLogConfigPath() . "/log.test.debug.ok.ini");
    }

    /**
     * 正常系
     * @TemplateCacheのexpire属性で指定した値が取得できること
     * @test
     * @dataProvider templateCacheProvider
     */
    public function okTemplateCacheExpire($action, $expire)
    {
        $reader = new TemplateCacheReader();
        $refClass = new \ReflectionClass("\WebStream\Test\TestData\TemplateCacheTest1");
        $reader->read($refClass, $action);
        $this->assertEquals($expire, $reader->getExpire());
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
     * @expectedException WebStream\Exception\AnnotationException
     */
    public function ngTemplateCacheExpire()
    {
        $reader = new TemplateCacheReader();
        $refClass = new \ReflectionClass("\WebStream\Test\TestData\TemplateCacheTest1");
        $reader->read($refClass, "error1");
    }

    /**
     * 異常系
     * @TemplateCacheのexpire属性に不正な値が指定されていた場合、値が取得できないこと
     * @test
     * @expectedException Doctrine\Common\Annotations\AnnotationException
     */
    public function ngTemplateCacheExpire2()
    {
        $reader = new TemplateCacheReader();
        $refClass = new \ReflectionClass("\WebStream\Test\TestData\TemplateCacheTest1");
        $reader->read($refClass, "error2");
    }
}
