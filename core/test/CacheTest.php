<?php
/**
 * Cacheクラスのテストクラス
 * @author Ryuichi TANAKA.
 * @since 2011/10/08
 */
require_once '../../core/AutoImport.php';
import("core/test/UnitTestBase");

class CacheTest extends UnitTestBase {
    private $cache_id = "cache_test";
    private $save_data_str = "abcde";
    
    public function setUp() {
        Logger::$level = Logger::DEBUG;
    }
    
    /**
     * 正常系
     * キャッシュファイルを作成できること
     */
    public function testOkCreateCache() {
        $cache = new Cache();
        $cache->save($this->cache_id, $this->save_data_str);
        $this->assertFileExists("/tmp/" . $this->cache_id . ".cache");
    }
    
    /**
     * 正常系
     * キャッシュファイルを指定ディレクトリに作成できること
     * @dataProvider testOkCreateCacheCustomDirProvider
     */
    public function testOkCreateCacheCustomDir($dir) {
        $cache = new Cache($dir);
        $cache->save($this->cache_id, $this->save_data_str);
        $this->assertFileExists($dir . "/" . $this->cache_id . ".cache");
    }
    
    /**
     * 正常系
     * キャッシュファイルにデータを保存できること
     * @dataProvider testOkSaveProvider
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
     * @dataProvider testOkMetaDataProvider
     */
    public function testOkMetaData($cache_id, $ttl = 60) {
        $cache = new Cache();
        $cache->save($cache_id, $this->save_data_str, $ttl);
        $meta = $cache->meta($cache_id);
        $this->assertEquals($meta["ttl"], $ttl);
    }
    
    /**
     * 正常系
     * キャッシュファイルを削除できること
     * @dataProvider testOkDeleteCacheProvider
     */
    public function testOkDeleteCache($cache_id) {
        $cache = new Cache();
        $cache->save($cache_id, $this->save_data_str);
        $this->assertFileExists("/tmp/" . $cache_id . ".cache");
        $this->assertTrue($cache->delete($cache_id));
        $this->assertFileNotExists("/tmp/" . $cache_id . ".cache");
    }
    
    /**
     * 正常系
     * キャッシュファイルを上書きして保存できること
     * @dataProvider testOkOverwriteSaveProvider
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
     * 異常系
     * 存在しないディレクトリにキャッシュファイルを保存できないこと
     * @dataProvider testNgInvalidSaveDirProvider
     */
    public function testNgInvalidSaveDir($dir) {
        $cache = new Cache($dir);
        $this->assertFalse($cache->save($this->cache_id, $this->save_data_str));
    }
    
    /**
     * 異常系
     * 書き込み権限のないディレクトリにキャッシュファイルを保存できないこと
     * @dataProvider testNgCreateCacheCustomDirProvider
     */
    public function testNgCreateCacheCustomDir($dir) {
        $cache = new Cache($dir);
        $this->assertFalse($cache->save($this->cache_id, $this->save_data_str));
    }
    
    /**
     * 異常系
     * 存在しないキャッシュファイルは削除できないこと
     * @dataProvider testNgInvalidDeletePathProvider
     */
    public function testNgInvalidDeletePath($cache_id) {
        $cache = new Cache();
        $this->assertFalse($cache->delete($cache_id));
    }
    
    /**
     * 異常系
     * 有効期限を過ぎたキャッシュは取得できず、キャッシュは削除されること
     * @dataProvider testNgTimeOverCacheProvider
     */
    public function testNgTimeOverCache($cache_id, $ttl) {
        $cache = new Cache();
        $cache->save($cache_id, $this->save_data_str, $ttl);
        $this->assertFileExists("/tmp/" . $cache_id . ".cache");
        sleep($ttl + 1);
        $this->assertNull($cache->get($cache_id));
        $this->assertFileNotExists("/tmp/" . $cache_id . ".cache");
    }
}
    