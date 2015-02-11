<?php
namespace WebStream;

use WebStream\Module\Logger;
use WebStream\Module\ClassLoader;
use WebStream\DI\ServiceLocator;

require_once dirname(__FILE__) . "/vendor/autoload.php";
require_once dirname(__FILE__) . '/core/WebStream/Module/Utility.php';
require_once dirname(__FILE__) . '/core/WebStream/Module/ClassLoader.php';
require_once dirname(__FILE__) . '/core/WebStream/Module/Logger.php';
require_once dirname(__FILE__) . '/core/WebStream/Module/Cache.php';
require_once dirname(__FILE__) . '/core/WebStream/Module/Container.php';
require_once dirname(__FILE__) . '/core/WebStream/Module/Functions.php';
require_once dirname(__FILE__) . '/core/WebStream/Module/HttpClient.php';
require_once dirname(__FILE__) . '/core/WebStream/Module/Security.php';
require_once dirname(__FILE__) . '/core/WebStream/Module/ValueProxy.php';
require_once dirname(__FILE__) . '/core/WebStream/DI/ServiceLocator.php';
require_once dirname(__FILE__) . '/core/WebStream/Core/Application.php';
require_once dirname(__FILE__) . '/core/WebStream/Core/CoreInterface.php';
require_once dirname(__FILE__) . '/core/WebStream/Core/CoreController.php';
require_once dirname(__FILE__) . '/core/WebStream/Core/CoreHelper.php';
require_once dirname(__FILE__) . '/core/WebStream/Core/CoreModel.php';
require_once dirname(__FILE__) . '/core/WebStream/Core/CoreService.php';
require_once dirname(__FILE__) . '/core/WebStream/Core/CoreView.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Base/IClass.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Base/IMethod.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Base/IMethods.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Base/IProperty.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Base/IRead.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Base/Annotation.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Reader/AnnotationReader.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Inject.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Autowired.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Header.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Filter.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Template.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/TemplateCache.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/ExceptionHandler.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Database.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Query.php';
require_once dirname(__FILE__) . '/core/WebStream/Database/DatabaseManager.php';
require_once dirname(__FILE__) . '/core/WebStream/Database/ConnectionManager.php';
require_once dirname(__FILE__) . '/core/WebStream/Database/EntityManager.php';
require_once dirname(__FILE__) . '/core/WebStream/Database/Driver/DatabaseDriver.php';
require_once dirname(__FILE__) . '/core/WebStream/Database/Driver/Mysql.php';
require_once dirname(__FILE__) . '/core/WebStream/Database/Driver/Postgresql.php';
require_once dirname(__FILE__) . '/core/WebStream/Database/Driver/Sqlite.php';
require_once dirname(__FILE__) . '/core/WebStream/Database/Query.php';
require_once dirname(__FILE__) . '/core/WebStream/Database/Result.php';
require_once dirname(__FILE__) . '/core/WebStream/Database/ResultEntity.php';
require_once dirname(__FILE__) . '/core/WebStream/Delegate/CoreDelegator.php';
require_once dirname(__FILE__) . '/core/WebStream/Delegate/ExceptionDelegator.php';
require_once dirname(__FILE__) . '/core/WebStream/Delegate/Resolver.php';
require_once dirname(__FILE__) . '/core/WebStream/Delegate/Router.php';
require_once dirname(__FILE__) . '/core/WebStream/Delegate/Validator.php';
require_once dirname(__FILE__) . '/core/WebStream/Exception/ApplicationException.php';
require_once dirname(__FILE__) . '/core/WebStream/Exception/UncatchableException.php';
require_once dirname(__FILE__) . '/core/WebStream/Exception/Extend/AnnotationException.php';
require_once dirname(__FILE__) . '/core/WebStream/Exception/Extend/ClassNotFoundException.php';
require_once dirname(__FILE__) . '/core/WebStream/Exception/Extend/IOException.php';
require_once dirname(__FILE__) . '/core/WebStream/Exception/Extend/CollectionException.php';
require_once dirname(__FILE__) . '/core/WebStream/Exception/Extend/CsrfException.php';
require_once dirname(__FILE__) . '/core/WebStream/Exception/Extend/DatabaseException.php';
require_once dirname(__FILE__) . '/core/WebStream/Exception/Extend/ForbiddenAccessException.php';
require_once dirname(__FILE__) . '/core/WebStream/Exception/Extend/InvalidArgumentException.php';
require_once dirname(__FILE__) . '/core/WebStream/Exception/Extend/InvalidRequestException.php';
require_once dirname(__FILE__) . '/core/WebStream/Exception/Extend/LoggerException.php';
require_once dirname(__FILE__) . '/core/WebStream/Exception/Extend/MethodNotFoundException.php';
require_once dirname(__FILE__) . '/core/WebStream/Exception/Extend/OutOfBoundsException.php';
require_once dirname(__FILE__) . '/core/WebStream/Exception/Extend/ResourceNotFoundException.php';
require_once dirname(__FILE__) . '/core/WebStream/Exception/Extend/RouterException.php';
require_once dirname(__FILE__) . '/core/WebStream/Exception/Extend/SessionTimeoutException.php';
require_once dirname(__FILE__) . '/core/WebStream/Exception/Extend/ValidateException.php';
require_once dirname(__FILE__) . '/core/WebStream/Http/Method/MethodInterface.php';
require_once dirname(__FILE__) . '/core/WebStream/Http/Method/Get.php';
require_once dirname(__FILE__) . '/core/WebStream/Http/Method/Post.php';
require_once dirname(__FILE__) . '/core/WebStream/Http/Method/Put.php';
require_once dirname(__FILE__) . '/core/WebStream/Http/Request.php';
require_once dirname(__FILE__) . '/core/WebStream/Http/Response.php';
require_once dirname(__FILE__) . '/core/WebStream/Http/Session.php';
require_once dirname(__FILE__) . '/config/routes.php';
require_once dirname(__FILE__) . '/config/validates.php';

Logger::init("config/log.ini");

$classLoader = new ClassLoader();
spl_autoload_register([$classLoader, "load"]);
register_shutdown_function('shutdownHandler');

// サービスロケータをロード
$container = ServiceLocator::getContainer();

// アプリケーションを起動する
$application = new \WebStream\Core\Application($container);
$application->run();
