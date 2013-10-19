<?php
namespace WebStream\Test\Sample;

use WebStream\Module\Logger;
use WebStream\DI\ServiceLocator;

require_once '../../Module/ClassLoader.php';
require_once '../../Module/Functions.php';

$classLoader = new \WebStream\Module\ClassLoader();
spl_autoload_register([$classLoader, "load"]);
register_shutdown_function('WebStream\Module\shutdownHandler');
$classLoader->import("core/WebStream/Test/Sample/config/routes.php");

// Annotations
$classLoader->load([
    "AbstractAnnotation",
    "Autowired",
    "Value",
    "Type",
    "Inject",
    "Filter",
    "Template",
    "Doctrine/Common/Annotations/AnnotationException"
]);

// // ログ出力ディレクトリ、ログレベルをテスト用に変更
Logger::init("core/WebStream/Test/Sample/config/log.ini");

// サービスロケータをロード
$container = ServiceLocator::getContainer();

// アプリケーションを起動
$controllerTestDir = "core/WebStream/Test/Sample/app";
$class = new \ReflectionClass("WebStream\Core\Application");
$instance = $class->newInstance($container);
$property = $class->getProperty("app_dir");
$property->setAccessible(true);
$property->setValue($instance, $controllerTestDir);
$method = $class->getMethod("run");
$method->invoke($instance);
