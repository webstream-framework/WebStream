<?php
namespace WebStream\Test\Sample;

use WebStream\Module\Logger;
use WebStream\Module\ClassLoader;
use WebStream\DI\ServiceLocator;

require_once dirname(__FILE__) . '/../../Module/Utility.php';
require_once dirname(__FILE__) . '/../../Module/ClassLoader.php';
require_once dirname(__FILE__) . '/../../Module/Logger.php';
require_once dirname(__FILE__) . '/../../Module/Cache.php';
require_once dirname(__FILE__) . '/../../Module/Container.php';
require_once dirname(__FILE__) . '/../../Module/Functions.php';
require_once dirname(__FILE__) . '/../../Module/HttpClient.php';
require_once dirname(__FILE__) . '/../../Module/Security.php';
require_once dirname(__FILE__) . '/../../Module/ValueProxy.php';
require_once dirname(__FILE__) . '/../../DI/ServiceLocator.php';
require_once dirname(__FILE__) . '/../../Core/Application.php';
require_once dirname(__FILE__) . '/../../Core/CoreInterface.php';
require_once dirname(__FILE__) . '/../../Core/CoreController.php';
require_once dirname(__FILE__) . '/../../Core/CoreHelper.php';
require_once dirname(__FILE__) . '/../../Core/CoreModel.php';
require_once dirname(__FILE__) . '/../../Core/CoreService.php';
require_once dirname(__FILE__) . '/../../Core/CoreView.php';
require_once dirname(__FILE__) . '/../../Annotation/AnnotationConstants.php';
require_once dirname(__FILE__) . '/../../Annotation/AbstractAnnotation.php';
require_once dirname(__FILE__) . '/../../Annotation/AnnotationReader.php';
require_once dirname(__FILE__) . '/../../Annotation/Autowired.php';
require_once dirname(__FILE__) . '/../../Annotation/AutowiredReader.php';
require_once dirname(__FILE__) . '/../../Annotation/Database.php';
require_once dirname(__FILE__) . '/../../Annotation/DatabaseReader.php';
require_once dirname(__FILE__) . '/../../Annotation/ExceptionHandler.php';
require_once dirname(__FILE__) . '/../../Annotation/ExceptionHandlerReader.php';
require_once dirname(__FILE__) . '/../../Annotation/Filter.php';
require_once dirname(__FILE__) . '/../../Annotation/FilterComponent.php';
require_once dirname(__FILE__) . '/../../Annotation/FilterReader.php';
require_once dirname(__FILE__) . '/../../Annotation/Header.php';
require_once dirname(__FILE__) . '/../../Annotation/HeaderReader.php';
require_once dirname(__FILE__) . '/../../Annotation/Inject.php';
require_once dirname(__FILE__) . '/../../Annotation/Query.php';
require_once dirname(__FILE__) . '/../../Annotation/QueryReader.php';
require_once dirname(__FILE__) . '/../../Annotation/Template.php';
require_once dirname(__FILE__) . '/../../Annotation/TemplateCache.php';
require_once dirname(__FILE__) . '/../../Annotation/TemplateCacheReader.php';
require_once dirname(__FILE__) . '/../../Annotation/TemplateComponent.php';
require_once dirname(__FILE__) . '/../../Annotation/TemplateReader.php';
require_once dirname(__FILE__) . '/../../Annotation/Type.php';
require_once dirname(__FILE__) . '/../../Annotation/Value.php';
require_once dirname(__FILE__) . '/../../Database/DatabaseManager.php';
require_once dirname(__FILE__) . '/../../Database/Driver/DatabaseDriver.php';
require_once dirname(__FILE__) . '/../../Database/Driver/Mysql.php';
require_once dirname(__FILE__) . '/../../Database/Driver/Postgresql.php';
require_once dirname(__FILE__) . '/../../Database/Driver/Sqlite.php';
require_once dirname(__FILE__) . '/../../Database/Query.php';
require_once dirname(__FILE__) . '/../../Database/Result.php';
require_once dirname(__FILE__) . '/../../Delegate/CoreDelegator.php';
require_once dirname(__FILE__) . '/../../Delegate/Resolver.php';
require_once dirname(__FILE__) . '/../../Delegate/Router.php';
require_once dirname(__FILE__) . '/../../Delegate/Validator.php';
require_once dirname(__FILE__) . '/../../Exception/AnnotationException.php';
require_once dirname(__FILE__) . '/../../Exception/ApplicationException.php';
require_once dirname(__FILE__) . '/../../Exception/ClassNotFoundException.php';
require_once dirname(__FILE__) . '/../../Exception/CollectionException.php';
require_once dirname(__FILE__) . '/../../Exception/CsrfException.php';
require_once dirname(__FILE__) . '/../../Exception/DatabaseException.php';
require_once dirname(__FILE__) . '/../../Exception/ForbiddenAccessException.php';
require_once dirname(__FILE__) . '/../../Exception/InvalidRequestException.php';
require_once dirname(__FILE__) . '/../../Exception/IOException.php';
require_once dirname(__FILE__) . '/../../Exception/LoggerException.php';
require_once dirname(__FILE__) . '/../../Exception/MethodNotFoundException.php';
require_once dirname(__FILE__) . '/../../Exception/ResourceNotFoundException.php';
require_once dirname(__FILE__) . '/../../Exception/RouterException.php';
require_once dirname(__FILE__) . '/../../Exception/SessionTimeoutException.php';
require_once dirname(__FILE__) . '/../../Exception/ValidateException.php';
require_once dirname(__FILE__) . '/../../Http/Method/MethodInterface.php';
require_once dirname(__FILE__) . '/../../Http/Method/Get.php';
require_once dirname(__FILE__) . '/../../Http/Method/Post.php';
require_once dirname(__FILE__) . '/../../Http/Method/Put.php';
require_once dirname(__FILE__) . '/../../Http/Request.php';
require_once dirname(__FILE__) . '/../../Http/Response.php';
require_once dirname(__FILE__) . '/../../Http/Session.php';
require_once dirname(__FILE__) . '/../../Vendor/Doctrine/Common/Annotations/AnnotationException.php';
require_once dirname(__FILE__) . '/config/routes.php';
require_once dirname(__FILE__) . '/config/validates.php';

// ログ出力ディレクトリ、ログレベルをテスト用に変更
Logger::init("core/WebStream/Test/Sample/config/log.ini");

$isXhprof = true;

// xhprof
if ($isXhprof) {
    xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
}

$classLoader = new ClassLoader();
$classLoader->test();
spl_autoload_register([$classLoader, "load"]);
register_shutdown_function('WebStream\Module\shutdownHandler');

// サービスロケータをロード
ServiceLocator::test();
$container = ServiceLocator::getContainer();

// アプリケーションを起動
$class = new \ReflectionClass("WebStream\Core\Application");
$instance = $class->newInstance($container);
$method = $class->getMethod("run");
$method->invoke($instance);

// サービスロケータをクリア
ServiceLocator::removeContainer();

if ($isXhprof) {
    $xhprofData = xhprof_disable();
    $xhprofRoot = '/Users/stay/workspace2/github-project-sample/';
    $projectName = 'WebStream';
    include_once $xhprofRoot . '/xhprof_lib/utils/xhprof_lib.php';
    include_once $xhprofRoot . '/xhprof_lib/utils/xhprof_runs.php';
    $xhprof_runs = new \XHProfRuns_Default();
    $xhprof_runs->save_run($xhprofData, $projectName);
}
