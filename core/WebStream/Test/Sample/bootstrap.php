<?php
namespace WebStream\Test\Sample;

use WebStream\Module\Logger;
use WebStream\Module\ClassLoader;
use WebStream\DI\ServiceLocator;

require_once dirname(__FILE__) . "/../../../../vendor/autoload.php";
require_once dirname(__FILE__) . '/../../Module/Utility.php';
require_once dirname(__FILE__) . '/../../Module/ClassLoader.php';
require_once dirname(__FILE__) . '/../../Module/Cache.php';
require_once dirname(__FILE__) . '/../../Module/Container.php';
require_once dirname(__FILE__) . '/../../Module/Functions.php';
require_once dirname(__FILE__) . '/../../Module/HttpClient.php';
require_once dirname(__FILE__) . '/../../Module/Logger.php';
require_once dirname(__FILE__) . '/../../Module/PropertyProxy.php';
require_once dirname(__FILE__) . '/../../Module/Security.php';
require_once dirname(__FILE__) . '/../../Module/Singleton.php';
require_once dirname(__FILE__) . '/../../Module/ValueProxy.php';
require_once dirname(__FILE__) . '/../../DI/ServiceLocator.php';
require_once dirname(__FILE__) . '/../../Core/Application.php';
require_once dirname(__FILE__) . '/../../Core/CoreInterface.php';
require_once dirname(__FILE__) . '/../../Core/CoreController.php';
require_once dirname(__FILE__) . '/../../Core/CoreHelper.php';
require_once dirname(__FILE__) . '/../../Core/CoreModel.php';
require_once dirname(__FILE__) . '/../../Core/CoreService.php';
require_once dirname(__FILE__) . '/../../Core/CoreView.php';
require_once dirname(__FILE__) . '/../../Annotation/Base/IClass.php';
require_once dirname(__FILE__) . '/../../Annotation/Base/IMethod.php';
require_once dirname(__FILE__) . '/../../Annotation/Base/IMethods.php';
require_once dirname(__FILE__) . '/../../Annotation/Base/IProperty.php';
require_once dirname(__FILE__) . '/../../Annotation/Base/IRead.php';
require_once dirname(__FILE__) . '/../../Annotation/Base/Annotation.php';
require_once dirname(__FILE__) . '/../../Annotation/Container/AnnotationContainer.php';
require_once dirname(__FILE__) . '/../../Annotation/Container/AnnotationListContainer.php';
require_once dirname(__FILE__) . '/../../Annotation/Container/ContainerFactory.php';
require_once dirname(__FILE__) . '/../../Annotation/Reader/AnnotationReader.php';
require_once dirname(__FILE__) . '/../../Annotation/Inject.php';
require_once dirname(__FILE__) . '/../../Annotation/Autowired.php';
require_once dirname(__FILE__) . '/../../Annotation/CsrfProtection.php';
require_once dirname(__FILE__) . '/../../Annotation/Database.php';
require_once dirname(__FILE__) . '/../../Annotation/ExceptionHandler.php';
require_once dirname(__FILE__) . '/../../Annotation/Filter.php';
require_once dirname(__FILE__) . '/../../Annotation/Header.php';
require_once dirname(__FILE__) . '/../../Annotation/Query.php';
require_once dirname(__FILE__) . '/../../Annotation/Template.php';
require_once dirname(__FILE__) . '/../../Annotation/Validate.php';
require_once dirname(__FILE__) . '/../../Database/DatabaseManager.php';
require_once dirname(__FILE__) . '/../../Database/ConnectionManager.php';
require_once dirname(__FILE__) . '/../../Database/EntityManager.php';
require_once dirname(__FILE__) . '/../../Database/Driver/DatabaseDriver.php';
require_once dirname(__FILE__) . '/../../Database/Driver/Mysql.php';
require_once dirname(__FILE__) . '/../../Database/Driver/Postgresql.php';
require_once dirname(__FILE__) . '/../../Database/Driver/Sqlite.php';
require_once dirname(__FILE__) . '/../../Database/Query.php';
require_once dirname(__FILE__) . '/../../Database/Result.php';
require_once dirname(__FILE__) . '/../../Database/ResultEntity.php';
require_once dirname(__FILE__) . '/../../Delegate/CoreDelegator.php';
require_once dirname(__FILE__) . '/../../Delegate/CoreExceptionDelegator.php';
require_once dirname(__FILE__) . '/../../Delegate/CoreExecuteDelegator.php';
require_once dirname(__FILE__) . '/../../Delegate/AnnotationDelegator.php';
require_once dirname(__FILE__) . '/../../Delegate/AnnotationDelegatorFactory.php';
require_once dirname(__FILE__) . '/../../Delegate/ExceptionDelegator.php';
require_once dirname(__FILE__) . '/../../Delegate/Resolver.php';
require_once dirname(__FILE__) . '/../../Delegate/Router.php';
require_once dirname(__FILE__) . '/../../Template/ITemplateEngine.php';
require_once dirname(__FILE__) . '/../../Template/Basic.php';
require_once dirname(__FILE__) . '/../../Template/Twig.php';
require_once dirname(__FILE__) . '/../../Validate/Rule/IValidate.php';
require_once dirname(__FILE__) . '/../../Validate/Rule/Equal.php';
require_once dirname(__FILE__) . '/../../Validate/Rule/Length.php';
require_once dirname(__FILE__) . '/../../Validate/Rule/Max.php';
require_once dirname(__FILE__) . '/../../Validate/Rule/MaxLength.php';
require_once dirname(__FILE__) . '/../../Validate/Rule/Min.php';
require_once dirname(__FILE__) . '/../../Validate/Rule/MinLength.php';
require_once dirname(__FILE__) . '/../../Validate/Rule/Number.php';
require_once dirname(__FILE__) . '/../../Validate/Rule/Range.php';
require_once dirname(__FILE__) . '/../../Validate/Rule/Regexp.php';
require_once dirname(__FILE__) . '/../../Validate/Rule/Required.php';
require_once dirname(__FILE__) . '/../../Exception/ApplicationException.php';
require_once dirname(__FILE__) . '/../../Exception/SystemException.php';
require_once dirname(__FILE__) . '/../../Exception/DelegateException.php';
require_once dirname(__FILE__) . '/../../Exception/Extend/AnnotationException.php';
require_once dirname(__FILE__) . '/../../Exception/Extend/ClassNotFoundException.php';
require_once dirname(__FILE__) . '/../../Exception/Extend/IOException.php';
require_once dirname(__FILE__) . '/../../Exception/Extend/CollectionException.php';
require_once dirname(__FILE__) . '/../../Exception/Extend/CsrfException.php';
require_once dirname(__FILE__) . '/../../Exception/Extend/DatabaseException.php';
require_once dirname(__FILE__) . '/../../Exception/Extend/ForbiddenAccessException.php';
require_once dirname(__FILE__) . '/../../Exception/Extend/InvalidArgumentException.php';
require_once dirname(__FILE__) . '/../../Exception/Extend/InvalidRequestException.php';
require_once dirname(__FILE__) . '/../../Exception/Extend/LoggerException.php';
require_once dirname(__FILE__) . '/../../Exception/Extend/MethodNotFoundException.php';
require_once dirname(__FILE__) . '/../../Exception/Extend/OutOfBoundsException.php';
require_once dirname(__FILE__) . '/../../Exception/Extend/ResourceNotFoundException.php';
require_once dirname(__FILE__) . '/../../Exception/Extend/RouterException.php';
require_once dirname(__FILE__) . '/../../Exception/Extend/SessionTimeoutException.php';
require_once dirname(__FILE__) . '/../../Exception/Extend/ValidateException.php';
require_once dirname(__FILE__) . '/../../Http/Method/MethodInterface.php';
require_once dirname(__FILE__) . '/../../Http/Method/Get.php';
require_once dirname(__FILE__) . '/../../Http/Method/Post.php';
require_once dirname(__FILE__) . '/../../Http/Method/Put.php';
require_once dirname(__FILE__) . '/../../Http/Request.php';
require_once dirname(__FILE__) . '/../../Http/Response.php';
require_once dirname(__FILE__) . '/../../Http/Session.php';
require_once dirname(__FILE__) . '/config/routes.php';

// デフォルトタイムゾーン
date_default_timezone_set('Asia/Tokyo');

// ログ出力ディレクトリ、ログレベルをテスト用に変更
Logger::init("core/WebStream/Test/Sample/config/log.ini");

$isXhprof = false;

// xhprof
if ($isXhprof) {
    xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
}

$classLoader = new ClassLoader();
$classLoader->test();
spl_autoload_register([$classLoader, "load"]);
// app以下をすべて読み込む
$classLoader->importAll("core/WebStream/Test/Sample/app", function ($filepath) {
    // MVCレイヤのクラスとview配下のphpファイルは除外
    return preg_match("/(?:(?:Controller|Service|Model)\.php|app\/views\/.+\.php)$/", $filepath) === 0;
});
register_shutdown_function('shutdownHandler');

// サービスロケータをロード
ServiceLocator::test();
$container = ServiceLocator::getContainer();

// アプリケーションを起動
$application = new \WebStream\Core\Application($container);
$application->run();

if ($isXhprof) {
    // TODO Vendor以下にもっていきたい。
    $xhprofData = xhprof_disable();
    $xhprofRoot = '/Users/mapserver2007/Dropbox/workspace/xhprof';
    $projectName = 'WebStream';
    include_once $xhprofRoot . '/xhprof_lib/utils/xhprof_lib.php';
    include_once $xhprofRoot . '/xhprof_lib/utils/xhprof_runs.php';
    $xhprof_runs = new \XHProfRuns_Default();
    $xhprof_runs->save_run($xhprofData, $projectName);
}
