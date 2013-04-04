<?php
namespace WebStream;
/**
 * ServiceLocatorクラス
 * @author Ryuichi TANAKA.
 * @since 2013/01/14
 */
class ServiceLocator {
    /** コンテナ */
    private static $container;

    /**
     * コンストラクタ
     */
    private function __construct() {}

    /**
     * コンテナを返却する
     * @return Object コンテナ
     */
    public static function getContainer() {
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
    private function createContainer() {
        $container = new Container();
        // Request
        $container->request = function() {
            return new Request();
        };
        // Response
        $container->response = function() {
            return new Response();
        };
        // Router
        $container->router(function($request) {
            return new Router($request);
        }, $container->request);

        return $container;
    }
}