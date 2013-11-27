<?php
namespace WebStream;

use WebStream\Module\Logger;
use WebStream\DI\ServiceLocator;

require_once '../../Module/ClassLoader.php';
require_once '../../Module/Functions.php';

$classLoader = new ClassLoader();
spl_autoload_register([$classLoader, "load"]);
register_shutdown_function('WebStream\Module\shutdownHandler');
$classLoader->import("config/routes.php");
$classLoader->import("config/validates.php");

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
    "Doctrine/Common/Annotations/AnnotationException"
]);

// Loggerを初期化
Logger::init();

// サービスロケータをロード
$container = ServiceLocator::getContainer();

// アプリケーションを起動する
$app = new Application($container);
$app->documentRoot("/WebStream"); // アプリケーション固有設定
$app->run();
