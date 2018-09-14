<?php
use WebStream\Log\Logger;
use WebStream\Log\LoggerConfigurationManager;
use WebStream\ClassLoader\ClassLoader;
use WebStream\Delegate\ServiceLocator;
use WebStream\DI\Injector;

require_once dirname(__FILE__) . "/vendor/autoload.php";
require_once dirname(__FILE__) . '/core/WebStream/Container/Container.php';
require_once dirname(__FILE__) . '/core/WebStream/Container/ValueProxy.php';
require_once dirname(__FILE__) . '/core/WebStream/DI/Injector.php';
require_once dirname(__FILE__) . '/core/WebStream/Util/CommonUtils.php';
require_once dirname(__FILE__) . '/core/WebStream/Util/ApplicationUtils.php';
require_once dirname(__FILE__) . '/core/WebStream/Util/SecurityUtils.php';
require_once dirname(__FILE__) . '/core/WebStream/Util/PropertyProxy.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Base/IAnnotatable.php';
require_once dirname(__FILE__) . '/core/WebStream/Log/LoggerUtils.php';
require_once dirname(__FILE__) . '/core/WebStream/Log/LoggerConfigurationManager.php';
require_once dirname(__FILE__) . '/core/WebStream/Log/Logger.php';
require_once dirname(__FILE__) . '/core/WebStream/Log/LoggerAdapter.php';
require_once dirname(__FILE__) . '/core/WebStream/Log/LoggerFormatter.php';
require_once dirname(__FILE__) . '/core/WebStream/Log/LoggerCache.php';
require_once dirname(__FILE__) . '/core/WebStream/Log/Outputter/IOutputter.php';
require_once dirname(__FILE__) . '/core/WebStream/Log/Outputter/ILazyWriter.php';
require_once dirname(__FILE__) . '/core/WebStream/Log/Outputter/BrowserOutputter.php';
require_once dirname(__FILE__) . '/core/WebStream/Log/Outputter/ConsoleOutputter.php';
require_once dirname(__FILE__) . '/core/WebStream/Log/Outputter/FileOutputter.php';
require_once dirname(__FILE__) . '/core/WebStream/IO/File.php';
require_once dirname(__FILE__) . '/core/WebStream/IO/InputStream.php';
require_once dirname(__FILE__) . '/core/WebStream/IO/OutputStream.php';
require_once dirname(__FILE__) . '/core/WebStream/IO/FileInputStream.php';
require_once dirname(__FILE__) . '/core/WebStream/IO/FileOutputStream.php';
require_once dirname(__FILE__) . '/core/WebStream/IO/StringInputStream.php';
require_once dirname(__FILE__) . '/core/WebStream/IO/ConsoleOutputStream.php';
require_once dirname(__FILE__) . '/core/WebStream/IO/Reader/InputStreamReader.php';
require_once dirname(__FILE__) . '/core/WebStream/IO/Reader/FileReader.php';
require_once dirname(__FILE__) . '/core/WebStream/IO/Writer/OutputStreamWriter.php';
require_once dirname(__FILE__) . '/core/WebStream/IO/Writer/FileWriter.php';
require_once dirname(__FILE__) . '/core/WebStream/IO/Writer/SimpleFileWriter.php';
require_once dirname(__FILE__) . '/core/WebStream/Util/Singleton.php';
require_once dirname(__FILE__) . '/core/WebStream/Delegate/ServiceLocator.php';
// require_once dirname(__FILE__) . '/core/WebStream/Module/Functions.php';
// require_once dirname(__FILE__) . '/core/WebStream/Http/HttpClient.php';
require_once dirname(__FILE__) . '/core/WebStream/Util/Security.php';
require_once dirname(__FILE__) . '/core/WebStream/ClassLoader/ClassLoader.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Container/AnnotationContainer.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Container/AnnotationListContainer.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Base/Annotation.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Base/IAnnotatable.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Base/IClass.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Base/IExtension.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Base/IMethod.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Base/IMethods.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Base/IProperty.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Base/IRead.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Base/Annotation.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Reader/AnnotationReader.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Reader/Extend/ExtendReader.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Reader/Extend/FilterExtendReader.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Reader/Extend/QueryExtendReader.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Attributes/Alias.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Attributes/CsrfProtection.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Attributes/Database.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Attributes/ExceptionHandler.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Attributes/Filter.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Attributes/Header.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Attributes/Query.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Attributes/Template.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Attributes/Validate.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Attributes/Ext/ValidateRule/IValidate.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Attributes/Ext/ValidateRule/Equal.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Attributes/Ext/ValidateRule/Length.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Attributes/Ext/ValidateRule/Max.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Attributes/Ext/ValidateRule/MaxLength.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Attributes/Ext/ValidateRule/Min.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Attributes/Ext/ValidateRule/MinLength.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Attributes/Ext/ValidateRule/Number.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Attributes/Ext/ValidateRule/Range.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Attributes/Ext/ValidateRule/Regexp.php';
require_once dirname(__FILE__) . '/core/WebStream/Annotation/Attributes/Ext/ValidateRule/Required.php';
require_once dirname(__FILE__) . '/core/WebStream/Cache/Driver/CacheDriverFactory.php';
require_once dirname(__FILE__) . '/core/WebStream/Cache/Driver/ICache.php';
require_once dirname(__FILE__) . '/core/WebStream/Cache/Driver/Apcu.php';
require_once dirname(__FILE__) . '/core/WebStream/Cache/Driver/Memcached.php';
require_once dirname(__FILE__) . '/core/WebStream/Cache/Driver/Redis.php';
require_once dirname(__FILE__) . '/core/WebStream/Cache/Driver/TemporaryFile.php';
require_once dirname(__FILE__) . '/core/WebStream/Database/DatabaseManager.php';
require_once dirname(__FILE__) . '/core/WebStream/Database/ConnectionManager.php';
require_once dirname(__FILE__) . '/core/WebStream/Database/EntityManager.php';
require_once dirname(__FILE__) . '/core/WebStream/Database/EntityProperty.php';
require_once dirname(__FILE__) . '/core/WebStream/Database/Driver/DatabaseDriver.php';
require_once dirname(__FILE__) . '/core/WebStream/Database/Driver/Mysql.php';
require_once dirname(__FILE__) . '/core/WebStream/Database/Driver/Postgresql.php';
require_once dirname(__FILE__) . '/core/WebStream/Database/Driver/Sqlite.php';
require_once dirname(__FILE__) . '/core/WebStream/Database/Query.php';
require_once dirname(__FILE__) . '/core/WebStream/Database/Result.php';
require_once dirname(__FILE__) . '/core/WebStream/Database/ResultEntity.php';
require_once dirname(__FILE__) . '/core/WebStream/Delegate/CoreDelegator.php';
require_once dirname(__FILE__) . '/core/WebStream/Delegate/CoreExecuteDelegator.php';
require_once dirname(__FILE__) . '/core/WebStream/Delegate/CoreExceptionDelegator.php';
require_once dirname(__FILE__) . '/core/WebStream/Delegate/AnnotationDelegator.php';
require_once dirname(__FILE__) . '/core/WebStream/Delegate/ExceptionDelegator.php';
require_once dirname(__FILE__) . '/core/WebStream/Delegate/Resolver.php';
require_once dirname(__FILE__) . '/core/WebStream/Delegate/Router.php';
require_once dirname(__FILE__) . '/core/WebStream/Template/ITemplateEngine.php';
require_once dirname(__FILE__) . '/core/WebStream/Template/Basic.php';
require_once dirname(__FILE__) . '/core/WebStream/Template/Twig.php';
require_once dirname(__FILE__) . '/core/WebStream/Exception/ApplicationException.php';
require_once dirname(__FILE__) . '/core/WebStream/Exception/SystemException.php';
require_once dirname(__FILE__) . '/core/WebStream/Exception/Delegate/ExceptionDelegator.php';
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
require_once dirname(__FILE__) . '/core/WebStream/Http/Request.php';
require_once dirname(__FILE__) . '/core/WebStream/Http/Response.php';
require_once dirname(__FILE__) . '/core/WebStream/Http/Session.php';
require_once dirname(__FILE__) . '/core/WebStream/Core/Application.php';
require_once dirname(__FILE__) . '/core/WebStream/Core/CoreInterface.php';
require_once dirname(__FILE__) . '/core/WebStream/Core/CoreController.php';
require_once dirname(__FILE__) . '/core/WebStream/Core/CoreHelper.php';
require_once dirname(__FILE__) . '/core/WebStream/Core/CoreModel.php';
require_once dirname(__FILE__) . '/core/WebStream/Core/CoreService.php';
require_once dirname(__FILE__) . '/core/WebStream/Core/CoreView.php';

// デフォルトタイムゾーン
date_default_timezone_set('Asia/Tokyo');

// ロガー設定を読み込む
$manager = new LoggerConfigurationManager(dirname(__FILE__) . '/config/log.ini');
$manager->load();

// ロガーを初期化
Logger::init($manager->getConfig());

// サービスロケータをロード
$container = ServiceLocator::getInstance()->getContainer();

$classLoader = new ClassLoader("./");
$classLoader->inject('logger', $container->logger);
spl_autoload_register([$classLoader, "load"]);

// app以下をすべて読み込む
$classLoader->importAll("app", function ($filepath) {
    // MVCレイヤのクラスとview配下のphpファイルは除外
    return preg_match("/(?:(?:Controller|Service|Model)\.php|app\/views\/.+\.php)$/", $filepath) === 0;
});
    
// アプリケーションを起動
$application = new \WebStream\Core\Application($container);
$application->run();
