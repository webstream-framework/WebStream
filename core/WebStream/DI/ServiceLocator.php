<?php
namespace WebStream\DI;

use WebStream\Module\Utility;
use WebStream\Module\Container;
use WebStream\Delegate\Router;
use WebStream\Delegate\Validator;
use WebStream\Delegate\CoreDelegator;
use WebStream\Annotation\Reader\AnnotationReader;
use WebStream\Annotation\Reader\AutowiredReader;
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
    use Utility;

    /** コンテナ */
    private static $container;

    /** テスト環境 */
    private static $isTest;

    /**
     * コンストラクタ
     */
    private function __construct()
    {
    }

    /**
     * テスト環境設定
     */
    public static function test()
    {
        self::$isTest = true;
    }

    /**
     * コンテナを返却する
     * @return object コンテナ
     */
    public static function getContainer()
    {
        if (!is_object(self::$container)) {
            $serviceLocator = new ServiceLocator();
            self::$container = $serviceLocator->createContainer(self::$isTest);
        }

        return self::$container;
    }

    /**
     * コンテナを削除する
     */
    public static function removeContainer()
    {
        self::$container = null;
    }

    /**
     * コンテナを作成する
     * @param boolean テスト環境フラグ
     * @return object コンテナ
     */
    private function createContainer($isTest)
    {
        $container = new Container();

        // Request
        $container->request = function () {
            $request = new Request();
            $reader = new AnnotationReader($request);
            $reader->read();

            $autowired = new AutowiredReader($reader);
            $autowired->inject($request);

            return $request;
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
            return new Router($container->request);
        };
        // Validator
        $container->validator = function () use (&$container) {
            return new Validator($container->request, $container->router);
        };
        // CoreDelegator
        $container->coreDelegator = function () use (&$container) {
            return new CoreDelegator($container);
        };
        // ApplicationRoot
        $container->applicationRoot = $isTest ? $this->getTestApplicationRoot() : $this->getRoot();
        // ApplicationDir
        $container->applicationDir = $isTest ? $this->getTestApplicationDir() : "app";

        return $container;
    }
}
