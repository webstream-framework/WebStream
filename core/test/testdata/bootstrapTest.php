<?php
require '../../../core/AutoImport.php';
// core以下のファイル、ルーティングルールをロード
importAll("core");
import("core/test/testdata/config/routes");

// ログ出力ディレクトリ、ログレベルをテスト用に変更
$class = new ReflectionClass("Logger");
$property = $class->getProperty("log_dir");
$property->setAccessible(true);
$property->setValue("log_dir", "core/test/testdata/log");

$controller_test_dir = "core/test/testdata/app";
$class = new ReflectionClass("Application");
$instance = $class->newInstance();
$property = $class->getProperty("app_dir");
$property->setAccessible(true);
$property->setValue($instance, $controller_test_dir);
$method = $class->getMethod("run");
$method->invoke($instance);
