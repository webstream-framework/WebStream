<?php
/**
 * テストクラスのベースクラス
 * @author Ryuichi TANAKA.
 * @since 2011/08/25
 */
importAll("core");

class UnitTestBase extends PHPUnit_Framework_TestCase {
    /** テスト用ルートURL */
    protected $root_url = "http://localhost/eclipse/stream/core/test/testdata";
    /** テスト用ログディレクトリ */
    protected $log_dir = "core/test/testdata/log";
    /** テスト用データベース(MySQL)設定ファイル */
    protected $config_path_mysql = "core/test/testdata/config/database.test.ini";
    /** テスト用のキャッシュファイル(777)ディレクトリ */
    protected $cache_dir_777 = "/core/test/testdata/cache_777";
    /** テスト用のキャッシュファイル(000)ディレクトリ */
    protected $cache_dir_000 = "/core/test/testdata/cache_000";
    
    protected $create_sql = <<< SQL
CREATE TABLE stream_test (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);    
SQL;

    protected $drop_sql = "DROP TABLE stream_test";
    
    /**
     * データプロバイダ
     */
    public function testOkResolveRootPathProvider() {
        return array(
            array('/', "test#test1")
        );
    }
    
    public function testOkResolveWithoutPlaceHolderProvider() {
        return array(
            array('/top', "test#test2")
        );
    }
    
    public function testOkResolveWithPlaceHolderProvider() {
        return array(
            array('/top/:id', "test#test3", "test3")
        );
    }
    
    public function testOkResolveCamelActionProvider() {
        return array(
            array('/action')
        );
    }
    
    public function testOkResolveWithPlaceHolderFormatProvider() {
        return array(
            array('/feed.rss')
        );
    }
    
    public function testOkSnakeControllerProvider() {
        return array(
            array("/snake")
        );
    }
    
    public function testOkUriWithEncodedStringProvider() {
        return array(
            array('/encoded/%E3%81%A6%E3%81%99%E3%81%A8', 'てすと')
        );
    }
    
    public function testNgResolveUnknownProvider() {
        return array(
            array('/notfound/controller', "notfound#test"),
            array('/notfound/action', "test#notfound")
        );
    }
    
    public function testNgResolveNotDefinePathProvider() {
        return array(
            array('/notdefine', "notdefine#path")
        );
    }
    
    public function testNgResolveRenderProvider() {
        return array(
            array('/render')
        );
    }
    
    public function testNgResolveLayoutProvider() {
        return array(
            array('/layout')
        );
    }

    public function testNgResolveRerdirectProvider() {
        return array(
            array('/redirect')
        );
    }
    
    public function testNgResolveLoadProvider() {
        return array(
            array('/load')
        );
    }

    public function testNgResolveBeforeProvider() {
        return array(
            array('/before')
        );
    }

    public function testNgResolveAfterProvider() {
        return array(
            array('/after')
        );
    }
    
    public function testNgResolveCamelControllerProvider() {
        return array(
            array('/error2', 'Test#test1'),
            array('/error3', 'teSt#test1'),
            array('/error3', '1test#test1')
        );
    }
    
    public function testOkWriteDebugProvider() {
        return array(
            array("debug message")
        );
    }
    
    public function testOkWriteDebugWithStackTraceProvider() {
        return array(
            array("debug message", "#0 /path/to/test.php(0)")
        );
    }
    
    public function testOkWriteInfoProvider() {
        return array(
            array("info message")
        );
    }
    
    public function testOkWriteInfoWithStackTraceProvider() {
        return array(
            array("info message", "#0 /path/to/test.php(0)")
        );
    }
    
    public function testOkWriteWarnProvider() {
        return array(
            array("warn message")
        );
    }
    
    public function testOkWriteWarnWithStackTraceProvider() {
        return array(
            array("warn message", "#0 /path/to/test.php(0)")
        );
    }
    
    public function testOkWriteErrorProvider() {
        return array(
            array("error message")
        );
    }
    
    public function testOkWriteErrorWithStackTraceProvider() {
        return array(
            array("error message", "#0 /path/to/test.php(0)")
        );
    }
    
    public function testOkWriteFatalProvider() {
        return array(
            array("fatal message")
        );
    }
    
    public function testOkWriteFatalWithStackTraceProvider() {
        return array(
            array("fatal message", "#0 /path/to/test.php(0)")
        );
    }
    
    public function testNgResolveInvalidPathProvider() {
        return array(
            array('/0aaa'),  // 先頭が数字
            array('/,aaa'),  // 先頭が半角英数ハイフンドットアンスコ以外
            array('/aaa,a'), // 途中にカンマ
        );
    }
    
    public function testNgProhibitPathProvider() {
        return array(
            array("/img"),
            array("/img/"),
            array("/img/xxx"),
            array("/js"),
            array("/js/"),
            array("/js/xxx"),
            array("/css"),
            array("/css/"),
            array("/css/xxx")
        );
    }
    
    public function testNgMultipleSnakeControllerProvider() {
        return array(
            array("/snake_ng1"),
            array("/snake_ng2")
        );
    }

    public function testNgUriWithoutUtf8EncodedStringProvider() {
        return array(
            array('/encoded/てすと', 'てすと'),
            array('/encoded/%A4%C6%A4%B9%A4%C8', 'てすと'), // EUC-JP
            array('/encoded/%82%C4%82%B7%82%C6', 'てすと')  // Shift_JIS
        );
    }
    
    public function testNgWithNullByteProvider() {
        return array(
            array('/encoded/%00')
        );
    }
    
    public function testOkDeleteInvisibleCharacterProvider() {
        return array(
            array('%E3%81%82%00%08%09', '%E3%81%82%09') // 00,08は制御文字
        );
    }
    
    public function testOkReplaceXSSStringsProvider() {
        return array(
            array('<div>\\a\t\n\r\r\n<!-- --><![CDATA[</div>',
                  '&lt;div&gt;\\\\a&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/><br/>&lt;!-- --&gt;&lt;![CDATA[&lt;/div&gt;')
        );
    }
    
    public function testOkCreateCacheCustomDirProvider() {
        return array(
            array(Utility::getRoot() . $this->cache_dir_777)
        );
    }
    
    public function testOkSaveProvider() {
        import("core/Cache");
        return array(
            array("cache_test_save_string", "abcde"),
            array("cache_test_save_integer", 12345),
            array("cache_test_save_array", array("abcde")),
            array("cache_test_save_object", new Cache())
        );
    }
    
    public function testOkMetaDataProvider() {
        return array(
            array("cache_test_metadata"),
            array("cache_test_metadata_ttl", 10)
        );
    }
    
    public function testOkDeleteCacheProvider() {
        return array(
            array("cache_test_delete")
        );
    }
    
    public function testOkOverwriteSaveProvider() {
        return array(
            array("cache_test_overwrite_save", "abcde", "fghij")
        );
    }
    
    public function testNgInvalidSaveDirProvider() {
        return array(
            array("/dummy")
        );
    }
    
    public function testNgCreateCacheCustomDirProvider() {
        return array(
            array(Utility::getRoot() . $this->cache_dir_000)
        );
    }
    
    public function testNgInvalidDeletePathProvider() {
        return array(
            array("dummy")
        );
    }
    
    public function testNgTimeOverCacheProvider() {
        return array(
            array("cache_test_time_over", 1)
        );
    }
}
