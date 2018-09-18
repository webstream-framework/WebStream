<?php
use WebStream\Log\Logger;
use WebStream\Log\LoggerConfigurationManager;
use WebStream\ClassLoader\ClassLoader;
use WebStream\Delegate\ServiceLocator;
use WebStream\DI\Injector;

require_once dirname(__FILE__) . "/vendor/autoload.php";
require_once dirname(__FILE__) . '/core/Container/Container.php';
require_once dirname(__FILE__) . '/core/Container/ValueProxy.php';
require_once dirname(__FILE__) . '/core/DI/Injector.php';
require_once dirname(__FILE__) . '/core/Util/CommonUtils.php';
require_once dirname(__FILE__) . '/core/Util/ApplicationUtils.php';
require_once dirname(__FILE__) . '/core/Util/SecurityUtils.php';
require_once dirname(__FILE__) . '/core/Util/PropertyProxy.php';
require_once dirname(__FILE__) . '/core/Annotation/Base/IAnnotatable.php';
require_once dirname(__FILE__) . '/core/Log/LoggerUtils.php';
require_once dirname(__FILE__) . '/core/Log/LoggerConfigurationManager.php';
require_once dirname(__FILE__) . '/core/Log/Logger.php';
require_once dirname(__FILE__) . '/core/Log/LoggerAdapter.php';
require_once dirname(__FILE__) . '/core/Log/LoggerFormatter.php';
require_once dirname(__FILE__) . '/core/Log/LoggerCache.php';
require_once dirname(__FILE__) . '/core/Log/Outputter/IOutputter.php';
require_once dirname(__FILE__) . '/core/Log/Outputter/ILazyWriter.php';
require_once dirname(__FILE__) . '/core/Log/Outputter/BrowserOutputter.php';
require_once dirname(__FILE__) . '/core/Log/Outputter/ConsoleOutputter.php';
require_once dirname(__FILE__) . '/core/Log/Outputter/FileOutputter.php';
require_once dirname(__FILE__) . '/core/IO/File.php';
require_once dirname(__FILE__) . '/core/IO/InputStream.php';
require_once dirname(__FILE__) . '/core/IO/OutputStream.php';
require_once dirname(__FILE__) . '/core/IO/FileInputStream.php';
require_once dirname(__FILE__) . '/core/IO/FileOutputStream.php';
require_once dirname(__FILE__) . '/core/IO/StringInputStream.php';
require_once dirname(__FILE__) . '/core/IO/ConsoleOutputStream.php';
require_once dirname(__FILE__) . '/core/IO/Reader/InputStreamReader.php';
require_once dirname(__FILE__) . '/core/IO/Reader/FileReader.php';
require_once dirname(__FILE__) . '/core/IO/Writer/OutputStreamWriter.php';
require_once dirname(__FILE__) . '/core/IO/Writer/FileWriter.php';
require_once dirname(__FILE__) . '/core/IO/Writer/SimpleFileWriter.php';
require_once dirname(__FILE__) . '/core/Util/Singleton.php';
require_once dirname(__FILE__) . '/core/Delegate/ServiceLocator.php';
// require_once dirname(__FILE__) . '/core/Module/Functions.php';
// require_once dirname(__FILE__) . '/core/Http/HttpClient.php';
require_once dirname(__FILE__) . '/core/Util/Security.php';
require_once dirname(__FILE__) . '/core/ClassLoader/ClassLoader.php';
require_once dirname(__FILE__) . '/core/Annotation/Container/AnnotationContainer.php';
require_once dirname(__FILE__) . '/core/Annotation/Container/AnnotationListContainer.php';
require_once dirname(__FILE__) . '/core/Annotation/Base/Annotation.php';
require_once dirname(__FILE__) . '/core/Annotation/Base/IAnnotatable.php';
require_once dirname(__FILE__) . '/core/Annotation/Base/IClass.php';
require_once dirname(__FILE__) . '/core/Annotation/Base/IExtension.php';
require_once dirname(__FILE__) . '/core/Annotation/Base/IMethod.php';
require_once dirname(__FILE__) . '/core/Annotation/Base/IMethods.php';
require_once dirname(__FILE__) . '/core/Annotation/Base/IProperty.php';
require_once dirname(__FILE__) . '/core/Annotation/Base/IRead.php';
require_once dirname(__FILE__) . '/core/Annotation/Base/Annotation.php';
require_once dirname(__FILE__) . '/core/Annotation/Reader/AnnotationReader.php';
require_once dirname(__FILE__) . '/core/Annotation/Reader/Extend/ExtendReader.php';
require_once dirname(__FILE__) . '/core/Annotation/Reader/Extend/FilterExtendReader.php';
require_once dirname(__FILE__) . '/core/Annotation/Reader/Extend/QueryExtendReader.php';
require_once dirname(__FILE__) . '/core/Annotation/Attributes/Alias.php';
require_once dirname(__FILE__) . '/core/Annotation/Attributes/CsrfProtection.php';
require_once dirname(__FILE__) . '/core/Annotation/Attributes/Database.php';
require_once dirname(__FILE__) . '/core/Annotation/Attributes/ExceptionHandler.php';
require_once dirname(__FILE__) . '/core/Annotation/Attributes/Filter.php';
require_once dirname(__FILE__) . '/core/Annotation/Attributes/Header.php';
require_once dirname(__FILE__) . '/core/Annotation/Attributes/Query.php';
require_once dirname(__FILE__) . '/core/Annotation/Attributes/Template.php';
require_once dirname(__FILE__) . '/core/Annotation/Attributes/Validate.php';
require_once dirname(__FILE__) . '/core/Annotation/Attributes/Ext/ValidateRule/IValidate.php';
require_once dirname(__FILE__) . '/core/Annotation/Attributes/Ext/ValidateRule/Equal.php';
require_once dirname(__FILE__) . '/core/Annotation/Attributes/Ext/ValidateRule/Length.php';
require_once dirname(__FILE__) . '/core/Annotation/Attributes/Ext/ValidateRule/Max.php';
require_once dirname(__FILE__) . '/core/Annotation/Attributes/Ext/ValidateRule/MaxLength.php';
require_once dirname(__FILE__) . '/core/Annotation/Attributes/Ext/ValidateRule/Min.php';
require_once dirname(__FILE__) . '/core/Annotation/Attributes/Ext/ValidateRule/MinLength.php';
require_once dirname(__FILE__) . '/core/Annotation/Attributes/Ext/ValidateRule/Number.php';
require_once dirname(__FILE__) . '/core/Annotation/Attributes/Ext/ValidateRule/Range.php';
require_once dirname(__FILE__) . '/core/Annotation/Attributes/Ext/ValidateRule/Regexp.php';
require_once dirname(__FILE__) . '/core/Annotation/Attributes/Ext/ValidateRule/Required.php';
require_once dirname(__FILE__) . '/core/Cache/Driver/CacheDriverFactory.php';
require_once dirname(__FILE__) . '/core/Cache/Driver/ICache.php';
require_once dirname(__FILE__) . '/core/Cache/Driver/Apcu.php';
require_once dirname(__FILE__) . '/core/Cache/Driver/Memcached.php';
require_once dirname(__FILE__) . '/core/Cache/Driver/Redis.php';
require_once dirname(__FILE__) . '/core/Cache/Driver/TemporaryFile.php';
require_once dirname(__FILE__) . '/core/Database/DatabaseManager.php';
require_once dirname(__FILE__) . '/core/Database/ConnectionManager.php';
require_once dirname(__FILE__) . '/core/Database/EntityManager.php';
require_once dirname(__FILE__) . '/core/Database/EntityProperty.php';
require_once dirname(__FILE__) . '/core/Database/Driver/DatabaseDriver.php';
require_once dirname(__FILE__) . '/core/Database/Driver/Mysql.php';
require_once dirname(__FILE__) . '/core/Database/Driver/Postgresql.php';
require_once dirname(__FILE__) . '/core/Database/Driver/Sqlite.php';
require_once dirname(__FILE__) . '/core/Database/Query.php';
require_once dirname(__FILE__) . '/core/Database/Result.php';
require_once dirname(__FILE__) . '/core/Database/ResultEntity.php';
require_once dirname(__FILE__) . '/core/Delegate/CoreDelegator.php';
require_once dirname(__FILE__) . '/core/Delegate/CoreExecuteDelegator.php';
require_once dirname(__FILE__) . '/core/Delegate/CoreExceptionDelegator.php';
require_once dirname(__FILE__) . '/core/Delegate/AnnotationDelegator.php';
require_once dirname(__FILE__) . '/core/Delegate/ExceptionDelegator.php';
require_once dirname(__FILE__) . '/core/Delegate/Resolver.php';
require_once dirname(__FILE__) . '/core/Delegate/Router.php';
require_once dirname(__FILE__) . '/core/Template/ITemplateEngine.php';
require_once dirname(__FILE__) . '/core/Template/Basic.php';
require_once dirname(__FILE__) . '/core/Template/Twig.php';
require_once dirname(__FILE__) . '/core/Exception/ApplicationException.php';
require_once dirname(__FILE__) . '/core/Exception/SystemException.php';
require_once dirname(__FILE__) . '/core/Exception/Delegate/ExceptionDelegator.php';
require_once dirname(__FILE__) . '/core/Exception/Extend/AnnotationException.php';
require_once dirname(__FILE__) . '/core/Exception/Extend/ClassNotFoundException.php';
require_once dirname(__FILE__) . '/core/Exception/Extend/IOException.php';
require_once dirname(__FILE__) . '/core/Exception/Extend/CollectionException.php';
require_once dirname(__FILE__) . '/core/Exception/Extend/CsrfException.php';
require_once dirname(__FILE__) . '/core/Exception/Extend/DatabaseException.php';
require_once dirname(__FILE__) . '/core/Exception/Extend/ForbiddenAccessException.php';
require_once dirname(__FILE__) . '/core/Exception/Extend/InvalidArgumentException.php';
require_once dirname(__FILE__) . '/core/Exception/Extend/InvalidRequestException.php';
require_once dirname(__FILE__) . '/core/Exception/Extend/LoggerException.php';
require_once dirname(__FILE__) . '/core/Exception/Extend/MethodNotFoundException.php';
require_once dirname(__FILE__) . '/core/Exception/Extend/OutOfBoundsException.php';
require_once dirname(__FILE__) . '/core/Exception/Extend/ResourceNotFoundException.php';
require_once dirname(__FILE__) . '/core/Exception/Extend/RouterException.php';
require_once dirname(__FILE__) . '/core/Exception/Extend/SessionTimeoutException.php';
require_once dirname(__FILE__) . '/core/Exception/Extend/ValidateException.php';
require_once dirname(__FILE__) . '/core/Http/Request.php';
require_once dirname(__FILE__) . '/core/Http/Response.php';
require_once dirname(__FILE__) . '/core/Http/Session.php';
require_once dirname(__FILE__) . '/core/Core/Application.php';
require_once dirname(__FILE__) . '/core/Core/CoreInterface.php';
require_once dirname(__FILE__) . '/core/Core/CoreController.php';
require_once dirname(__FILE__) . '/core/Core/CoreHelper.php';
require_once dirname(__FILE__) . '/core/Core/CoreModel.php';
require_once dirname(__FILE__) . '/core/Core/CoreService.php';
require_once dirname(__FILE__) . '/core/Core/CoreView.php';

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
