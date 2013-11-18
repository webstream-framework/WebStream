<?php
namespace WebStream\DI;

use WebStream\Module\Container;
use WebStream\Delegate\Router;
use WebStream\Delegate\Validator;
use WebStream\Annotation\AutowiredReader;
use WebStream\Http\Response;
use WebStream\Http\Session;

/**
 * ServiceLocatorクラス
 * @author Ryuichi TANAKA.
 * @since 2013/01/14
 */
class ServiceLocator
{
    /** コンテナ */
    private static $container;

    /**
     * コンストラクタ
     */
    private function __construct()
    {
    }

    /**
     * コンテナを返却する
     * @return Object コンテナ
     */
    public static function getContainer()
    {
        if (!is_object(self::$container)) {
            $serviceLocator = new ServiceLocator();
            self::$container = $serviceLocator->createContainer();
        }

        return self::$container;
    }

    /**
     * コンテナを作成する
     * @return Object コンテナ
     */
    private function createContainer()
    {
        $container = new Container();

        // Request
        $container->request = function() {
            $refClass = new \ReflectionClass("\WebStream\Http\Request");
            $autowired = new AutowiredReader();
            $autowired->read($refClass);
            return $autowired->getReceiver();
        };
        // Response
        $container->response = function() {
            return new Response();
        };
        // Session
        $container->session = function() {
            return new Session();
        };
        // Router
        $container->router = function() use (&$container) {
            return new Router($container->request);
        };
        // Validator
        $container->validator = function() use (&$container) {
            return new Validator($container->request, $container->router);
        };

        return $container;
    }
}
