<?php
/**
 * テストクラスのベースクラス
 * @author Ryuichi TANAKA.
 * @since 2011/08/25
 */
class UnitTestBase extends PHPUnit_Framework_TestCase {
    /** テスト用ルートURL */
    protected $document_root = "http://localhost/eclipse/";
    protected $project_name = "stream";
    protected $testdata_dir = "/core/test/testdata";
    protected $root_url;

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

    public function setUp() {
        $this->loadModule();
    }

    private function loadModule() {
        require_once $this->getRoot() . "/core/AutoImport.php";
        importAll("core");
        $this->root_url = $this->document_root . $this->project_name . $this->testdata_dir;
    }

    private function getRoot() {
        $current = dirname(__FILE__);
        $path_hierarchy_list = explode(DIRECTORY_SEPARATOR, $current);
        array_pop($path_hierarchy_list);
        array_pop($path_hierarchy_list);
        $project_root = implode("/", $path_hierarchy_list);

        return is_dir($project_root) ? $project_root : null;
    }

    /**
     * データプロバイダ
     */
    public function resolveRootPathProvider() {
        return array(
            array('/', "test#test1")
        );
    }

    public function resolveWithoutPlaceHolderProvider() {
        return array(
            array('/top', "test#test2")
        );
    }

    public function resolveWithPlaceHolderProvider() {
        return array(
            array('/top/:id', "test#test3", "test3")
        );
    }

    public function resolveCamelActionProvider() {
        return array(
            array('/action')
        );
    }

    public function resolveWithPlaceHolderFormatProvider() {
        return array(
            array('/feed.rss')
        );
    }

    public function snakeControllerProvider() {
        return array(
            array("/snake")
        );
    }

    public function uriWithEncodedStringProvider() {
        return array(
            array('/encoded/%E3%81%A6%E3%81%99%E3%81%A8', 'てすと')
        );
    }

    public function resolveUnknownProvider() {
        return array(
            array('/notfound/controller', "notfound#test"),
            array('/notfound/action', "test#notfound")
        );
    }

    public function resolveNotDefinePathProvider() {
        return array(
            array('/notdefine', "notdefine#path")
        );
    }

    public function resolveRenderProvider() {
        return array(
            array('/render')
        );
    }

    public function resolveLayoutProvider() {
        return array(
            array('/layout')
        );
    }

    public function resolveRerdirectProvider() {
        return array(
            array('/redirect')
        );
    }

    public function resolveLoadProvider() {
        return array(
            array('/load')
        );
    }

    public function resolveBeforeProvider() {
        return array(
            array('/before')
        );
    }

    public function resolveAfterProvider() {
        return array(
            array('/after')
        );
    }

    public function resolveCamelControllerProvider() {
        return array(
            array('/error2', 'Test#test1'),
            array('/error3', 'teSt#test1'),
            array('/error3', '1test#test1')
        );
    }

    public function writeDebugProvider() {
        return array(
            array("debug message")
        );
    }

    public function writeDebugWithStackTraceProvider() {
        return array(
            array("debug message", "#0 /path/to/test.php(0)")
        );
    }

    public function writeInfoProvider() {
        return array(
            array("info message")
        );
    }

    public function writeInfoWithStackTraceProvider() {
        return array(
            array("info message", "#0 /path/to/test.php(0)")
        );
    }

    public function writeWarnProvider() {
        return array(
            array("warn message")
        );
    }

    public function writeWarnWithStackTraceProvider() {
        return array(
            array("warn message", "#0 /path/to/test.php(0)")
        );
    }

    public function writeErrorProvider() {
        return array(
            array("error message")
        );
    }

    public function writeErrorWithStackTraceProvider() {
        return array(
            array("error message", "#0 /path/to/test.php(0)")
        );
    }

    public function writeFatalProvider() {
        return array(
            array("fatal message")
        );
    }

    public function writeFatalWithStackTraceProvider() {
        return array(
            array("fatal message", "#0 /path/to/test.php(0)")
        );
    }

    public function resolveInvalidPathProvider() {
        return array(
            array('/0aaa'),  // 先頭が数字
            array('/,aaa'),  // 先頭が半角英数ハイフンドットアンスコ以外
            array('/aaa,a'), // 途中にカンマ
        );
    }

    public function prohibitPathProvider() {
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

    public function multipleSnakeControllerProvider() {
        return array(
            array("/snake_ng1"),
            array("/snake_ng2")
        );
    }

    public function uriWithoutUtf8EncodedStringProvider() {
        return array(
            array('/encoded/%A4%C6%A4%B9%A4%C8', 'てすと'), // EUC-JP
            array('/encoded/%82%C4%82%B7%82%C6', 'てすと')  // Shift_JIS
        );
    }

    public function withNullByteProvider() {
        return array(
            array('/encoded/%00')
        );
    }

    public function deleteInvisibleCharacterProvider() {
        return array(
            array('%E3%81%82%00%08%09', '%E3%81%82%09') // 00,08は制御文字
        );
    }

    public function replaceXSSStringsProvider() {
        return array(
            array('<div>\\a\t\n\r\r\n<!-- --><![CDATA[</div>',
                  '&lt;div&gt;\\\\a&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/><br/>&lt;!-- --&gt;&lt;![CDATA[&lt;/div&gt;')
        );
    }

    public function createCacheCustomDirProvider() {
        $this->loadModule();
        return array(
            array(Utility::getRoot() . $this->cache_dir_777)
        );
    }

    public function saveProvider() {
        import("core/Cache");
        return array(
            array("cache_test_save_string", "abcde"),
            array("cache_test_save_integer", 12345),
            array("cache_test_save_array", array("abcde")),
            array("cache_test_save_object", new Cache())
        );
    }

    public function metaDataProvider() {
        return array(
            array("cache_test_metadata"),
            array("cache_test_metadata_ttl", 10)
        );
    }

    public function deleteCacheProvider() {
        return array(
            array("cache_test_delete")
        );
    }

    public function overwriteSaveProvider() {
        return array(
            array("cache_test_overwrite_save", "abcde", "fghij")
        );
    }

    public function invalidSaveDirProvider() {
        return array(
            array("/dummy")
        );
    }

    public function createCacheCustomDir000Provider() {
        $this->loadModule();
        return array(
            array(Utility::getRoot() . $this->cache_dir_000)
        );
    }

    public function invalidDeletePathProvider() {
        return array(
            array("dummy")
        );
    }

    public function timeOverCacheProvider() {
        return array(
            array("cache_test_time_over", 1)
        );
    }
    
    public function resolveSimilarUrlProvider() {
        return array(
            array("/similar/name", "similar1"),
            array("/similar/name/2", "similar2")
        );
    }
}
