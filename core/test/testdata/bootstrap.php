<?php
namespace WebStream;
require '../../../core/AutoImport.php';
require '../../../core/Functions.php';
use WebStream\Application;
use WebStream\Logger;
use WebStream\AutoImport;

function __autoload($class_name) {
    import("core/test/testdata/config/" . $class_name);
}

// core以下のファイル、ルーティングルール、バリデーションルールをロード
importAll("core");
import("core/test/testdata/config/routes");
import("core/test/testdata/config/validates");

// ログ出力ディレクトリ、ログレベルをテスト用に変更
Logger::init("core/test/testdata/config/log.ini");

$controller_test_dir = "core/test/testdata/app";
$class = new \ReflectionClass("WebStream\Application");
$instance = $class->newInstance();
$property = $class->getProperty("app_dir");
$property->setAccessible(true);
$property->setValue($instance, $controller_test_dir);
$method = $class->getMethod("run");
$method->invoke($instance);
