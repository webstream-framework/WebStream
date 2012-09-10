<?php
namespace WebStream\Test;
use WebStream\Logger;
use WebStream\Utility;
use WebStream\Cache;
/**
 * テストクラスのベースクラス
 * @author Ryuichi TANAKA.
 * @since 2011/08/25
 */
class UnitTestBase extends \PHPUnit_Framework_TestCase {
    /** テスト用ルートURL */
    protected $document_root = "http://localhost/";
    protected $project_name = "WebStream";
    protected $testdata_dir = "/core/test/testdata";
    protected $root_url;

    /** テスト用ログ設定ファイルディレクトリ */
    protected $config_path_log = "core/test/testdata/config/log_config/";
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
        Logger::init("core/test/testdata/config/log.ini");
    }

    protected function loadModule() {
        require_once $this->getRoot() . "/core/AutoImport.php";
        require_once $this->getRoot() . "/core/Functions.php";
        \WebStream\importAll("core");
        $this->root_url = $this->document_root . $this->project_name . $this->testdata_dir;
    }

    protected function getRoot() {
        $current = dirname(__FILE__);
        $path_hierarchy_list = explode(DIRECTORY_SEPARATOR, $current);
        array_pop($path_hierarchy_list);
        array_pop($path_hierarchy_list);
        $project_root = implode("/", $path_hierarchy_list);
        return is_dir($project_root) ? $project_root : null;
    }
    
    protected function logHead($config_path) {
        $log = Utility::parseConfig($config_path);
        $log_path = realpath(Utility::getRoot() . "/" . $log["path"]);
        $file = file($log_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return array_shift($file);
    }

    protected function logTail($config_path) {
        $log = Utility::parseConfig($config_path);
        $log_path = realpath(Utility::getRoot() . "/" . $log["path"]);
        $file = file($log_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return array_pop($file);
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
            array('/action', "testAction"),
            array('/action2', "testAction2")
        );
    }

    public function resolveWithPlaceHolderFormatProvider() {
        return array(
            array('/feed.rss')
        );
    }

    public function snakeControllerProvider() {
        return array(
            array("/snake", "snake"),
            array("/snake2", "snake2")
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
    
    public function logLevelDebugProvider() {
        return array(
            array("DEBUG", "log.test.debug.ok.ini", "log message for debug1."),
            array("DEBUG", "log.test.debug.ok.ini", "log message for debug2.", "#0 /path/to/test.php(0)"),
            array("INFO",  "log.test.debug.ok.ini", "log message for debug3."),
            array("INFO",  "log.test.debug.ok.ini", "log message for debug4.", "#0 /path/to/test.php(0)"),
            array("WARN",  "log.test.debug.ok.ini", "log message for debug5."),
            array("WARN",  "log.test.debug.ok.ini", "log message for debug6.", "#0 /path/to/test.php(0)"),
            array("ERROR", "log.test.debug.ok.ini", "log message for debug7."),
            array("ERROR", "log.test.debug.ok.ini", "log message for debug8.", "#0 /path/to/test.php(0)"),
            array("FATAL", "log.test.debug.ok.ini", "log message for debug9."),
            array("FATAL", "log.test.debug.ok.ini", "log message for debug10.", "#0 /path/to/test.php(0)"),
        );
    }
    
    public function logLevelInfoProvider() {
        return array(
            array("DEBUG", "log.test.info.ok.ini", "log message for info1."),
            array("DEBUG", "log.test.info.ok.ini", "log message for info2.", "#0 /path/to/test.php(0)"),
            array("INFO",  "log.test.info.ok.ini", "log message for info3."),
            array("INFO",  "log.test.info.ok.ini", "log message for info4.", "#0 /path/to/test.php(0)"),
            array("WARN",  "log.test.info.ok.ini", "log message for info5."),
            array("WARN",  "log.test.info.ok.ini", "log message for info6.", "#0 /path/to/test.php(0)"),
            array("ERROR", "log.test.info.ok.ini", "log message for info7."),
            array("ERROR", "log.test.info.ok.ini", "log message for info8.", "#0 /path/to/test.php(0)"),
            array("FATAL", "log.test.info.ok.ini", "log message for info9."),
            array("FATAL", "log.test.info.ok.ini", "log message for info10.", "#0 /path/to/test.php(0)"),
        );
    }
    
    public function logLevelWarnProvider() {
        return array(
            array("DEBUG", "log.test.warn.ok.ini", "log message for warn1."),
            array("DEBUG", "log.test.warn.ok.ini", "log message for warn2.", "#0 /path/to/test.php(0)"),
            array("INFO",  "log.test.warn.ok.ini", "log message for warn3."),
            array("INFO",  "log.test.warn.ok.ini", "log message for warn4.", "#0 /path/to/test.php(0)"),
            array("WARN",  "log.test.warn.ok.ini", "log message for warn5."),
            array("WARN",  "log.test.warn.ok.ini", "log message for warn6.", "#0 /path/to/test.php(0)"),
            array("ERROR", "log.test.warn.ok.ini", "log message for warn7."),
            array("ERROR", "log.test.warn.ok.ini", "log message for warn8.", "#0 /path/to/test.php(0)"),
            array("FATAL", "log.test.warn.ok.ini", "log message for warn9."),
            array("FATAL", "log.test.warn.ok.ini", "log message for warn10.", "#0 /path/to/test.php(0)"),
        );
    }
    
    public function logLevelErrorProvider() {
        return array(
            array("DEBUG", "log.test.error.ok.ini", "log message for error1."),
            array("DEBUG", "log.test.error.ok.ini", "log message for error2.", "#0 /path/to/test.php(0)"),
            array("INFO",  "log.test.error.ok.ini", "log message for error3."),
            array("INFO",  "log.test.error.ok.ini", "log message for error4.", "#0 /path/to/test.php(0)"),
            array("WARN",  "log.test.error.ok.ini", "log message for error5."),
            array("WARN",  "log.test.error.ok.ini", "log message for error6.", "#0 /path/to/test.php(0)"),
            array("ERROR", "log.test.error.ok.ini", "log message for error7."),
            array("ERROR", "log.test.error.ok.ini", "log message for error8.", "#0 /path/to/test.php(0)"),
            array("FATAL", "log.test.error.ok.ini", "log message for error9."),
            array("FATAL", "log.test.error.ok.ini", "log message for error10.", "#0 /path/to/test.php(0)"),
        );
    }
    
    public function logLevelFatalProvider() {
        return array(
            array("DEBUG", "log.test.fatal.ok.ini", "log message for fatal1."),
            array("DEBUG", "log.test.fatal.ok.ini", "log message for fatal2.", "#0 /path/to/test.php(0)"),
            array("INFO",  "log.test.fatal.ok.ini", "log message for fatal3."),
            array("INFO",  "log.test.fatal.ok.ini", "log message for fatal4.", "#0 /path/to/test.php(0)"),
            array("WARN",  "log.test.fatal.ok.ini", "log message for fatal5."),
            array("WARN",  "log.test.fatal.ok.ini", "log message for fatal6.", "#0 /path/to/test.php(0)"),
            array("ERROR", "log.test.fatal.ok.ini", "log message for fatal7."),
            array("ERROR", "log.test.fatal.ok.ini", "log message for fatal8.", "#0 /path/to/test.php(0)"),
            array("FATAL", "log.test.fatal.ok.ini", "log message for fatal9."),
            array("FATAL", "log.test.fatal.ok.ini", "log message for fatal10.", "#0 /path/to/test.php(0)"),
        );
    }
    
    public function rotateCycleDayWithinProvider() {
        return array(
            array("log.test.ok1.rotate.ini", 1),
            array("log.test.ok1.rotate.ini", 23)
        );
    }
    
    public function rotateCycleDayProvider() {
        return array(
            array("log.test.ok1.rotate.ini", 24),
            array("log.test.ok1.rotate.ini", 25)
        );
    }
    
    public function rotateCycleWeekWithinProvider() {
        return array(
            array("log.test.ok2.rotate.ini", 24),
            array("log.test.ok2.rotate.ini", 24 * 7 -1)
        );
    }
    
    public function rotateCycleWeekProvider() {
        return array(
            array("log.test.ok2.rotate.ini", 24 * 7),
            array("log.test.ok2.rotate.ini", 24 * 7 + 1)
        );
    }
    
    public function rotateCycleMonthWithinProvider() {
        $day_of_month = 24 * intval(date("t", time()));
        return array(
            array("log.test.ok3.rotate.ini", 24),
            array("log.test.ok3.rotate.ini", $day_of_month - 1)
        );
    }
    
    public function rotateCycleMonthProvider() {
        $day_of_month = 24 * intval(date("t", time()));
        return array(
            array("log.test.ok3.rotate.ini", $day_of_month),
            array("log.test.ok3.rotate.ini", $day_of_month + 1)
        );
    }
    
    public function rotateCycleYearWithinProvider() {
        $day_of_year = 24 * 365;
        $year = date("Y");
        if (($year % 4 === 0 && $year % 100 !== 0) || $year % 400 === 0) {
            $day_of_year = 24 * 366;
        }
        return array(
            array("log.test.ok4.rotate.ini", 24),
            array("log.test.ok4.rotate.ini", $day_of_year - 1)
        );
    }
    
    public function rotateCycleYearProvider() {
        $day_of_year = 24 * 365;
        $year = date("Y");
        if (($year % 4 === 0 && $year % 100 !== 0) || $year % 400 === 0) {
            $day_of_year = 24 * 366;
        }
        return array(
            array("log.test.ok4.rotate.ini", $day_of_year),
            array("log.test.ok4.rotate.ini", $day_of_year + 1)
        );
    }
    
    public function notFoundRotateCycleConfigProvider() {
        return array(
            array("log.test.ng1.rotate.ini")
        );
    }
    
    public function invalidRotateCycleConfigProvider() {
        return array(
            array("log.test.ng2.rotate.ini")
        );
    }
    
    public function notFoundRotateSizeConfigProvider() {
        return array(
            array("log.test.ng3.rotate.ini")
        );
    }
    
    public function invalidRotateSizeConfigProvider() {
        return array(
            array("log.test.ng4.rotate.ini")
        );
    }
    
    public function rotateSizeProvider() {
        return array(
            array("log.test.ok5.rotate.ini", 1024),
            array("log.test.ok5.rotate.ini", 1025)
        );
    }
    
    public function rotateSizeWithinProvider() {
        return array(
            array("log.test.ok5.rotate.ini", 1023),
            array("log.test.ok5.rotate.ini", 0)
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
        \WebStream\import("core/Cache");
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
    
    public function noServiceClass() {
        return array(
            array("/no_service", "no service class")
        );
    }
    
    public function noServiceMethod() {
        return array(
            array("/no_service2", "no service method")
        );
    }
    
    public function noServiceNoModel() {
        return array(
            array('/no_service_no_model', "TestNoServiceAndModelService and TestNoServiceAndModelModel is not defined.")
        );
    }
    
    public function existServiceExistModelNoMethod() {
        return array(
            array('/exist_service_exist_model_no_method', "TestExistServiceExistModelNoMethodService#get is not defined.")
        );
    }
    
    public function noServiceExistModelNoMethod() {
        return array(
            array("/no_service_exist_model_no_method", "TestNoServiceExistModelNoMethodModel#get is not defined.")
        );
    }
    
    public function existServiceNoModelNoMethod() {
        return array(
            array("/exist_service_no_model_no_method", "TestExistServiceNoModelNoMethodService#get is not defined.")
        );
    }
    
    public function camel2SnakeProvider() {
        return array(
            array("test", "Test"),
            array("test_hoge", "TestHoge"),
            array("test_hoge", "testHoge"),
            array("test_hoge_hoge", "TestHogeHoge"),
            array("test_hoge_hoge", "testHogeHoge")
        );
    }

    public function snake2UpperCamelProvider() {
        return array(
            array("Test", "test"),
            array("TestHoge", "test_hoge"),
            array("TestHogeHoge", "test_hoge_hoge")
        );
    }

    public function snake2LowerCamelProvider() {
        return array(
            array("testHoge", "test_hoge"),
            array("testHogeHoge", "test_hoge_hoge")
        );
    }
    
    public function renderTemplateProvider() {
        return array(
            array('/view1', "test_view"),
            array('/view2', "test_view_sub"),
            array('/view3', "test_aaa_bbb_view"),
            array('/view4', "test_aaa_bbb_view_sub"),
            array('/view5', "test_view_sub_test")
        );
    }
    
    public function renderLayoutTemplateProvider() {
        return array(
            array('/layout1', "test_layout_view"),
            array('/layout2', "test_layout_view_sub")
        );
    }
    
    public function sendParamFromControllerToModelProvider() {
        return array(
            array('/exist_service_exist_model_exist_model_method_param', "abc"),
            array('/exist_service_exist_model_exist_model_method_params', "abcdef")
        );
    }
    
    public function getResourceProvider() {
        return array(
            array("/img/sample.png", "image/png"),
            array("/img/sample2.PNG", "image/png"),
            array("/css/sample.css", "text/css"),
            array("/css/sample2.CSS", "text/css"),
            array("/js/sample.js", "text/javascript"),
            array("/js/sample2.JS", "text/javascript")
        );
    }
    
    public function getFileProvider() {
        return array(
            array("/file/sample.xml", "application/xml"),
            array("/file/sample.rdf", "application/xml"),
            array("/file/sample.atom", "application/xml"),
            array("/file/sample.pdf", "application/pdf"),
            array("/file/sample.json", "application/json"),
            array("/file/sample.txt", "text/plain"),
            array("/file/sample.html", "text/html"),
            array("/file/sample.htm", "text/html"),
            array("/file/sample.php", "application/octet-stream")
        );
    }
    
    public function renderMethodProvider() {
        return array(
            array("/resource/html", "text/html"),
            array("/resource/rss", "application/xml"),
            array("/resource/xml", "application/xml"),
            array("/resource/atom", "application/xml"),
            array("/resource/rdf", "application/xml"),
        );
    }
    
    public function notFoundRenderMethodProvider() {
        return array(
            array("/notfound_render", "WebStream\\TestController#render_dummy is not defined.")
        );
    }
    
    public function getRequestProvider() {
        return array(
            array("/get_request", "name", "test")
        );
    }
    
    public function postRequestProvider() {
        return array(
            array("/post_request", "name", "test")
        );
    }
    
    public function setSessionProvider() {
        return array(
            array("/set_session", "name", "test", "/get_session")
        );
    }
    
    public function csrfCheckRequestProvider() {
        return array(
            array("/csrf_get_view"),
            array("/csrf_post_view")
        );
    }
    
    public function retrurnStatusCodeProvider() {
        return array(
            array("/status301", 301),
            array("/status400", 400),
            array("/status403", 403),
            array("/status404", 404),
            array("/status500", 500),
            array("/status_unknown", 500)
        );
    }
    
    public function encodeAndDecodeProvider() {
        return array(
            array("abcde"),
            array(12345),
            array(array("name" => "azunyan")),
            array(new UnitTestBase())
        );
    }
    
    public function writeArrayDataProvider() {
        return array(
            array("cache_test_write_array", array("array"))
        );
    }
    
    public function xml2ArrayProvider() {
        return array(
            array("http://rss.dailynews.yahoo.co.jp/fc/rss.xml")
        );
    }
    
    public function useHelperProvider() {
        return array(
            array("/helper1", '<div class="test">html</div>'),
            array("/helper2", '<div class="test">html</div>'),
            array("/helper3", '<div class="test">$name</div>')
        );
    }
    
    public function helperFunctionNameProvider() {
        return array(
            array("/helper4", "test"),
            array("/helper5", "test")
        );
    }
    
    public function notFoundHelperMethodProvider() {
        return array(
            array("/helper6", "TestHelper#notfound is not defined.")
        );
    }
    
    public function notEntryReferenceInFormProvider() {
        $html = "<body><form action=\".\">\n";
        $html.= "    <input type=\"text\" value=\"attr\">\n";
        $html.= "</form></body>";
        return array(
            array("/attr", $html)
        );
    }
    
    public function executeSQL() {
        return array(
            array("select * from users"),
            array("select * from users where user_name = :name", array("name" => "yui"))
        );
    }
}
