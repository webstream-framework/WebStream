<?php
namespace WebStream\DI;

use WebStream\Module\Utility\ApplicationUtils;
use WebStream\Module\Singleton;
use WebStream\Module\Container;
use WebStream\Log\Logger;
use WebStream\Log\LoggerAdapter;
use WebStream\Delegate\Router;
use WebStream\Delegate\CoreDelegator;
use WebStream\Delegate\AnnotationDelegator;
use WebStream\Http\Request;
use WebStream\Http\Response;
use WebStream\Http\Session;

/**
 * ServiceLocatorクラス
 * @author Ryuichi TANAKA.
 * @since 2013/01/14
 */
class ServiceLocator
{
    use Singleton, ApplicationUtils;

    /**
     * コンテナをクリア
     */
    public function removeContainer()
    {
        $this->__clear();
    }

    /**
     * コンテナを作成する
     * @param boolean テスト環境フラグ
     * @return object コンテナ
     */
    public function getContainer()
    {
        $container = new Container();

        // Request
        $container->request = function () {
            return new Request();
        };
        // Response
        $container->response = function () {
            return new Response();
        };
        // Session
        $container->session = function () {
            return new Session();
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
            $coreDelegator = new CoreDelegator($container);
            $coreDelegator->inject('logger', $container->logger);

            return $coreDelegator;
        };
        // AnnotationDelegator
        $container->annotationDelegator = function () use (&$container) {
            return new AnnotationDelegator($container);
        };
        // LoggerAdapter
        $container->logger = function () {
            return new LoggerAdapter(Logger::getInstance());
        };
        // twig
        $container->twig = function () {
            Twig_Autoloader::register();
        };

        $applicationRoot = $this->getApplicationRoot();
        $container->applicationInfo = function() use ($applicationRoot) {
            $info = new Container();
            $info->applicationRoot = $applicationRoot;
            $info->applicationDir = "app";
            $info->sharedDir = "_shared";
            $info->publicDir = "_public";
            $info->cacheDir = "_cache";
            $info->cachePrefix = "webstream-cache-";
            $info->routeConfigPath = "/config/routes.yml";

            return $info;
        };

        return $container;
    }
}
