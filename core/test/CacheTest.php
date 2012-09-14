<?php
namespace WebStream\Test;
use WebStream\Cache;
use WebStream\Logger;
/**
 * Cacheクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/10/08
 */
require_once 'UnitTestBase.php';

class CacheTest extends UnitTestBase {
    private $cache_id = "cache_test";
    private $save_data_str = "abcde";
    private $cache_dir;
    
    public function setUp() {
        $this->cache_dir = PHP_OS === "WIN32" || PHP_OS === "WINNT" ? "C:\\tmp\\" : "/tmp/";
        Logger::init($this->config_path_log . "log.test.debug.ok.ini");
    }
    
    /**
     * 正常系
     * キャッシュファイルを作成できること
     */
    public function testOkCreateCache() {
        $cache = new Cache();
        $cache->save($this->cache_id, $this->save_data_str);
        $this->assertFileExists($this->cache_dir . $this->cache_id . ".cache");
    }
    
    /**
     * 正常系
     * キャッシュファイルを指定ディレクトリに作成できること
     * @dataProvider createCacheCustomDirProvider
     */
    public function testOkCreateCacheCustomDir($dir) {
        $cache = new Cache($dir);
        $cache->save($this->cache_id, $this->save_data_str);
        $this->assertFileExists($dir . "/" . $this->cache_id . ".cache");
    }
    
    /**
     * 正常系
     * キャッシュファイルにデータを保存できること
     * @dataProvider saveProvider
     */
    public function testOkSave($cache_id, $data) {
        $cache = new Cache();
        $cache->delete($cache_id);
        $cache->save($cache_id, $data);
        $this->assertEquals($cache->get($cache_id), $data);
    }
    
    /**
     * 正常系
     * キャッシュファイルからメタデータを取得できること
     * @dataProvider metaDataProvider
     */
    public function testOkMetaData($cache_id, $ttl = 60) {
        $cache = new Cache();
        $cache->save($cache_id, $this->save_data_str, $ttl, true);
        $meta = $cache->meta($cache_id);
        $this->assertEquals($meta["ttl"], $ttl);
    }
    
    /**
     * 正常系
     * キャッシュファイルを削除できること
     * @dataProvider deleteCacheProvider
     */
    public function testOkDeleteCache($cache_id) {
        $cache = new Cache();
        $cache->save($cache_id, $this->save_data_str);
        $this->assertFileExists($this->cache_dir . $cache_id . ".cache");
        $this->assertTrue($cache->delete($cache_id));
        $this->assertFileNotExists($this->cache_dir . $cache_id . ".cache");
    }
    
    /**
     * 正常系
     * キャッシュファイルを上書きして保存できること
     * @dataProvider overwriteSaveProvider
     */
    public function testOkOverwriteSave($cache_id, $before_data, $after_data) {
        $cache = new Cache();
        $cache->delete($cache_id);
        $cache->save($cache_id, $before_data);
        $this->assertEquals($cache->get($cache_id), $before_data);
        $cache->save($cache_id, $after_data, 60, true);
        $this->assertEquals($cache->get($cache_id), $after_data);
    }
    
    /**
     * 正常系
     * 配列データをキャッシュ出来ること
     * @dataProvider writeArrayDataProvider
     */
    public function testOkWriteArrayData($cache_id, $data) {
        $cache = new Cache();
        $cache->delete($cache_id);
        $cache->save($cache_id, $data);
        $this->assertEquals($cache->get($cache_id), $data);
    }
    
    /**
     * 異常系
     * 存在しないディレクトリにキャッシュファイルを保存できないこと
     * @dataProvider invalidSaveDirProvider
     */
    public function testNgInvalidSaveDir($dir) {
        $cache = new Cache($dir);
        $this->assertFalse($cache->save($this->cache_id, $this->save_data_str));
    }
    
    /**
     * 異常系
     * 書き込み権限のないディレクトリにキャッシュファイルを保存できないこと
     * @dataProvider createCacheCustomDirProvider
     */
    public function testNgCreateCacheCustomDir($dir) {
        $cache = new Cache($dir);
        $this->assertFalse($cache->save($this->cache_id, $this->save_data_str));
    }
    
    /**
     * 異常系
     * 存在しないキャッシュファイルは削除できないこと
     * @dataProvider invalidDeletePathProvider
     */
    public function testNgInvalidDeletePath($cache_id) {
        $cache = new Cache();
        $this->assertFalse($cache->delete($cache_id));
    }
    
    /**
     * 異常系
     * 有効期限を過ぎたキャッシュは取得できず、キャッシュは削除されること
     * @dataProvider timeOverCacheProvider
     */
    public function testNgTimeOverCache($cache_id, $ttl) {
        $cache = new Cache();
        $cache->save($cache_id, $this->save_data_str, $ttl);
        $this->assertFileExists($this->cache_dir . $cache_id . ".cache");
        sleep($ttl + 1);
        $this->assertNull($cache->get($cache_id));
        $this->assertFileNotExists($this->cache_dir . $cache_id . ".cache");
    }
}