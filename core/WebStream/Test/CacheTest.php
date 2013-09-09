<?php
namespace WebStream\Test;

use WebStream\Module\Cache;
use WebStream\Module\Logger;
use WebStream\Module\Utility;
use WebStream\Test\DataProvider\CacheProvider;

require_once 'TestBase.php';
require_once 'TestConstant.php';
require_once 'DataProvider/CacheProvider.php';

/**
 * CacheTest
 * @author Ryuichi TANAKA.
 * @since 2011/10/08
 * @version 0.4
 */
class CacheTest extends TestBase
{
    use CacheProvider, Utility, TestConstant;

    private $cacheId = "cache_test";
    private $save_data_str = "abcde";
    private $cacheDir;

    public function setUp()
    {
        parent::setUp();
        $this->cacheDir = PHP_OS === "WIN32" || PHP_OS === "WINNT" ? "C:\\Windows\\Temp" : "/tmp/";
        Logger::init($this->getLogConfigPath() . "/log.test.debug.ok.ini");
    }

    /**
     * 正常系
     * キャッシュファイルを作成できること
     * @test
     */
    public function okCreateCache()
    {
        $cache = new Cache();
        $cache->save($this->cacheId, $this->save_data_str);
        $this->assertFileExists($this->cacheDir . $this->cacheId . ".cache");
    }

    /**
     * 正常系
     * キャッシュファイルを指定ディレクトリに作成できること
     * @test
     */
    public function testOkCreateCacheCustomDir()
    {
        $cacheDir = $this->getRoot() . "/" . $this->getCacheDir777();
        $cache = new Cache($cacheDir);
        $cache->save($this->cacheId, $this->save_data_str);
        $this->assertFileExists($cacheDir . "/" . $this->cacheId . ".cache");
    }

    /**
     * 正常系
     * キャッシュファイルにデータを保存できること
     * @test
     * @dataProvider saveProvider
     */
    public function okSave($cacheId, $data)
    {
        $cache = new Cache();
        $cache->delete($cacheId);
        $cache->save($cacheId, $data);
        $this->assertEquals($cache->get($cacheId), $data);
    }

    /**
     * 正常系
     * キャッシュファイルからメタデータを取得できること
     * @test
     * @dataProvider metaDataProvider
     */
    public function okMetaData($cacheId, $ttl = 60)
    {
        $cache = new Cache();
        $cache->save($cacheId, $this->save_data_str, $ttl, true);
        $meta = $cache->meta($cacheId);
        $this->assertEquals($meta["ttl"], $ttl);
    }

    /**
     * 正常系
     * キャッシュファイルを削除できること
     * @test
     * @dataProvider deleteCacheProvider
     */
    public function okDeleteCache($cacheId)
    {
        $cache = new Cache();
        $cache->save($cacheId, $this->save_data_str);
        $this->assertFileExists($this->cacheDir . $cacheId . ".cache");
        $this->assertTrue($cache->delete($cacheId));
        $this->assertFileNotExists($this->cacheDir . $cacheId . ".cache");
    }

    /**
     * 正常系
     * キャッシュファイルを上書きして保存できること
     * @test
     * @dataProvider overwriteSaveProvider
     */
    public function okOverwriteSave($cacheId, $beforeData, $afterData)
    {
        $cache = new Cache();
        $cache->delete($cacheId);
        $cache->save($cacheId, $beforeData);
        $this->assertEquals($cache->get($cacheId), $beforeData);
        $cache->save($cacheId, $afterData, 60, true);
        $this->assertEquals($cache->get($cacheId), $afterData);
    }

    /**
     * 正常系
     * 配列データをキャッシュ出来ること
     * @test
     * @dataProvider writeArrayDataProvider
     */
    public function okWriteArrayData($cacheId, $data)
    {
        $cache = new Cache();
        $cache->delete($cacheId);
        $cache->save($cacheId, $data);
        $this->assertEquals($cache->get($cacheId), $data);
    }

    /**
     * 異常系
     * 存在しないディレクトリにキャッシュファイルを保存できないこと
     * @test
     * @dataProvider invalidSaveDirProvider
     */
    public function ngInvalidSaveDir($dir)
    {
        $cache = new Cache($dir);
        $this->assertFalse($cache->save($this->cacheId, $this->save_data_str));
    }

    /**
     * 異常系
     * 書き込み権限のないディレクトリにキャッシュファイルを保存できないこと
     * @test
     */
    public function ngCreateCacheCustomDir()
    {
        $cacheDir = $this->getRoot() . "/" . $this->getCacheDir000();
        $cache = new Cache($cacheDir);
        $this->assertFalse($cache->save($this->cacheId, $this->save_data_str));
    }

    /**
     * 異常系
     * 存在しないキャッシュファイルは削除できないこと
     * @test
     * @dataProvider invalidDeletePathProvider
     */
    public function ngInvalidDeletePath($cacheId)
    {
        $cache = new Cache();
        $this->assertFalse($cache->delete($cacheId));
    }

    /**
     * 異常系
     * 有効期限を過ぎたキャッシュは取得できず、キャッシュは削除されること
     * @test
     * @dataProvider timeOverCacheProvider
     */
    public function ngTimeOverCache($cacheId, $ttl)
    {
        $cache = new Cache();
        $cache->save($cacheId, $this->save_data_str, $ttl);
        $this->assertFileExists($this->cacheDir . $cacheId . ".cache");
        sleep($ttl + 1);
        $this->assertNull($cache->get($cacheId));
        $this->assertFileNotExists($this->cacheDir . $cacheId . ".cache");
    }
}
