<?php
namespace WebStream\Cache\Test;

require_once dirname(__FILE__) . '/../Modules/DI/Injector.php';
require_once dirname(__FILE__) . '/../Modules/Container/Container.php';
require_once dirname(__FILE__) . '/../Modules/Container/ValueProxy.php';
require_once dirname(__FILE__) . '/../Modules/IO/InputStream.php';
require_once dirname(__FILE__) . '/../Modules/IO/OutputStream.php';
require_once dirname(__FILE__) . '/../Modules/IO/File.php';
require_once dirname(__FILE__) . '/../Modules/IO/FileInputStream.php';
require_once dirname(__FILE__) . '/../Modules/IO/FileOutputStream.php';
require_once dirname(__FILE__) . '/../Modules/IO/Reader/InputStreamReader.php';
require_once dirname(__FILE__) . '/../Modules/IO/Reader/FileReader.php';
require_once dirname(__FILE__) . '/../Modules/IO/Writer/OutputStreamWriter.php';
require_once dirname(__FILE__) . '/../Modules/IO/Writer/FileWriter.php';
require_once dirname(__FILE__) . '/../Driver/ICache.php';
require_once dirname(__FILE__) . '/../Driver/Apcu.php';
require_once dirname(__FILE__) . '/../Driver/Memcached.php';
require_once dirname(__FILE__) . '/../Driver/Redis.php';
require_once dirname(__FILE__) . '/../Driver/TemporaryFile.php';
require_once dirname(__FILE__) . '/../Driver/CacheDriverFactory.php';
require_once dirname(__FILE__) . '/Modules/InvalidArgumentException.php';
require_once dirname(__FILE__) . '/Modules/IOException.php';
require_once dirname(__FILE__) . '/Providers/CacheProvider.php';

use WebStream\Cache\Test\Providers\CacheProvider;

/**
 * CacheTest
 * @author Ryuichi TANAKA.
 * @since 2016/07/06
 * @version 0.7
 */
class CacheTest extends \PHPUnit\Framework\TestCase
{
    use CacheProvider;

    /**
     * 正常系
     * キャッシュを新規追加できること
     * @test
     * @dataProvider cacheProvider
     */
    public function okAddCache($cache)
    {
        $ttt = $cache->add("key", "value1", 0, false);
        $this->assertEquals($cache->get("key"), "value1");
    }

    /**
     * 正常系
     * キャッシュを上書きできること
     * @test
     * @dataProvider cacheProvider
     */
    public function okAddOverwriteCache($cache)
    {
        $cache->add("key", "value1", 0, true);
        $this->assertEquals($cache->get("key"), "value1");
    }

    /**
     * 正常系
     * キャッシュを削除できること
     * @test
     * @dataProvider cacheProvider
     */
    public function okDeleteCache($cache)
    {
        $cache->add("key", "value1", 0, true);
        $cache->delete("key");
        $this->assertNull($cache->get("key"));
    }

    /**
     * 正常系
     * キャッシュを削除できること
     * @test
     * @dataProvider cacheProvider
     */
    public function okClearCache($cache)
    {
        $cache->add("key1", "value1", 0, true);
        $cache->add("key2", "value2", 0, true);
        $cache->clear();
        $this->assertNull($cache->get("key1"));
        $this->assertNull($cache->get("key2"));
    }
}
