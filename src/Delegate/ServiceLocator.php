<?php
namespace WebStream\Delegate;

use WebStream\Container\Container;
use WebStream\Log\Logger;
use WebStream\Log\LoggerAdapter;
use WebStream\Log\Outputter\FileOutputter;
use WebStream\Delegate\Router;
use WebStream\Delegate\CoreDelegator;
use WebStream\Delegate\AnnotationDelegator;
use WebStream\Http\Request;
use WebStream\Http\Response;
use WebStream\Http\Session;
use WebStream\Util\ApplicationUtils;
use WebStream\Util\Singleton;

/**
 * ServiceLocatorクラス
 * @author Ryuichi TANAKA.
 * @since 2013/01/14
 * @version 0.7
 */
class ServiceLocator
{
    use Singleton, ApplicationUtils;

    /**
     * コンテナを作成する
     * @param boolean テスト環境フラグ
     * @return object コンテナ
     */
    public function getContainer()
    {
        $container = new Container();

        // LoggerAdapter
        $container->logger = function () {
            $instance = Logger::getInstance();
            $instance->setOutputter([
                new FileOutputter($instance->getConfig()->logPath)
            ]);

            return new LoggerAdapter($instance);
        };
        // Request
        $container->request = function () use (&$container) {
            $request = new Request();
            $request->inject('logger', $container->logger);

            return $request->getContainer();
        };
        // Response
        $container->response = function () use (&$container) {
            $response = new Response();
            $response->inject('logger', $container->logger);

            return $response;
        };
        // Session
        $container->session = function () use (&$container) {
            $session = new Session();
            $session->inject('logger', $container->logger);

            return $session;
        };
        // Router
        $container->router = function () use (&$container) {
            // Router
            $config = \Spyc::YAMLLoad($container->applicationInfo->applicationRoot . $container->applicationInfo->routeConfigPath);
            $router = new Router($config, $container->request);
            $router->inject('logger', $container->logger)
                   ->inject('applicationInfo', $container->applicationInfo);
            $router->resolve();

            return $router->getRoutingResult();
        };
        // CoreDelegator
        $container->coreDelegator = function () use (&$container) {
            return new CoreDelegator($container);
        };
        // AnnotationDelegator
        $container->annotationDelegator = function () use (&$container) {
            return new AnnotationDelegator($container);
        };
        // twig
        $container->twig = function () {
            Twig_Autoloader::register();
        };
        // Application Info
        $applicationRoot = $this->getApplicationRoot();
        $container->applicationInfo = function () use ($applicationRoot) {
            $info = new Container();
            $info->applicationRoot = $applicationRoot;
            $info->applicationDir = "app";
            $info->sharedDir = "_shared";
            $info->publicDir = "_public";
            $info->cacheDir = "_cache";
            $info->cachePrefix = "webstream-cache-";
            $info->routeConfigPath = "/config/routes.yml";
            $info->validateRuleDir = "core/WebStream/Validate/Rule/";
            $info->externalLibraryRoot = $applicationRoot . "/vendor";

            return $info;
        };

        return $container;
    }
}
