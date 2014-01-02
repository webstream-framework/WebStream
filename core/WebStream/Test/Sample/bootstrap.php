<?php
namespace WebStream\Test\Sample;

use WebStream\Module\Logger;
use WebStream\Module\ClassLoader;
use WebStream\DI\ServiceLocator;

require_once '../../Module/ClassLoader.php';
require_once '../../Module/Functions.php';

$classLoader = new ClassLoader();
spl_autoload_register([$classLoader, "load"]);
register_shutdown_function('WebStream\Module\shutdownHandler');
$classLoader->import("core/WebStream/Test/Sample/config/routes.php");
$classLoader->import("core/WebStream/Test/Sample/config/validates.php");

// Annotations
$classLoader->load([
    "AbstractAnnotation",
    "Autowired",
    "Value",
    "Type",
    "Inject",
    "Filter",
    "Template",
    "TemplateCache",
    "Header",
    "ExceptionHandler",
    "Database",
    "Query",
    "Doctrine/Common/Annotations/AnnotationException"
]);

// // ログ出力ディレクトリ、ログレベルをテスト用に変更
Logger::init("core/WebStream/Test/Sample/config/log.ini");

// サービスロケータをロード
$container = ServiceLocator::getContainer();

// アプリケーションを起動
$controllerTestDir = "core/WebStream/Test/Sample/app";
$appRootDir = "core/WebStream/Test/Sample";
$class = new \ReflectionClass("WebStream\Core\Application");
$instance = $class->newInstance($container);
$property = $class->getProperty("app_dir");
$property->setAccessible(true);
$property->setValue($instance, $controllerTestDir);
$property = $class->getProperty("app_root");
$property->setAccessible(true);
$property->setValue($instance, $appRootDir);
$method = $class->getMethod("documentRoot");
$method->invoke($instance, "/WebStream/core/WebStream/Test/Sample/");
$method = $class->getMethod("run");
$method->invoke($instance);

// サービスロケータをクリア
ServiceLocator::removeContainer();
