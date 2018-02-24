<?php
namespace WebStream\Delegate;

use WebStream\Core\CoreView;
use WebStream\Container\Container;
use WebStream\ClassLoader\ClassLoader;
use WebStream\Util\ApplicationUtils;
use WebStream\Util\CommonUtils;
use WebStream\Exception\Extend\ClassNotFoundException;

/**
 * CoreDelegator
 * @author Ryuichi TANAKA.
 * @since 2011/11/30
 * @version 0.7
 */
class CoreDelegator
{
    use CommonUtils;
    use ApplicationUtils
    {
        ApplicationUtils::getNamespace as getDefinedNamespace;
    }

    /**
     * @var Container DIコンテナ
     */
    private $container;

    /**
     * @var Logger ロガー
     */
    private $logger;

    /**
     * @var Container Coreレイヤコンテナ
     */
    private $coreContainer;

    /**
     * Constructor
     * @param Container 依存コンテナ
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->logger = $container->logger;
        $this->coreContainer = new Container();
        $this->initialize();
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->coreContainer->remove("controller");
        $this->coreContainer->remove("view");
        $this->coreContainer->remove("service");
        $this->coreContainer->remove("model");
        $this->coreContainer->remove("helper");
        $this->logger->debug("CoreDelegator container is clear.");
    }

    /**
     * 各レイヤのオブジェクトをコンテナに設定する
     */
    private function initialize()
    {
        $container = $this->container;
        $classLoader = new ClassLoader($this->getApplicationRoot());
        $classLoader->inject('logger', $container->logger)
                    ->inject('applicationInfo', $container->applicationInfo);
        $pageName = $this->getPageName();
        $serviceClassName = $pageName . "Service";
        $modelClassName   = $pageName . "Model";
        $helperClassName  = $pageName . "Helper";
        $appRoot = $container->applicationInfo->applicationRoot . "/app";
        $controllerNamespace = $this->getNamespace($appRoot, $container->router->controller);
        $serviceNamespace    = $this->getNamespace($appRoot, $serviceClassName);
        $modelNamespace      = $this->getNamespace($appRoot, $modelClassName);
        $helperNamespace     = $this->getNamespace($appRoot, $helperClassName);

        // Controller
        $this->coreContainer->controller = function () use ($container, $controllerNamespace) {
            $controllerClassPath = $controllerNamespace . "\\" . $container->router->controller;
            if (!class_exists($controllerClassPath)) {
                throw new ClassNotFoundException("Undefined class path: " . $controllerClassPath);
            }

            $controller = new $controllerClassPath();
            $controller->inject('request', $container->request)
                       ->inject('response', $container->response)
                       ->inject('session', $container->session)
                       ->inject('coreDelegator', $container->coreDelegator)
                       ->inject('logger', $container->logger);

            $container->logger->debug("Controller start.");

            return $controller;
        };

        // View
        $this->coreContainer->view = function () use ($container) {
            $view = new CoreView($container);
            $view->inject('logger', $container->logger);

            $container->logger->debug("View start.");

            return $view;
        };

        // Service
        if ($serviceNamespace !== null) {
            $serviceClassPath = $serviceNamespace . "\\" . $serviceClassName;
            $this->coreContainer->service = function () use ($container, $classLoader, $serviceClassPath, $serviceClassName) {
                if ($classLoader->import($container->applicationInfo->applicationDir . "/services/" . $serviceClassName . ".php")) {
                    $service = new $serviceClassPath();
                    $service->inject('coreDelegator', $container->coreDelegator)
                            ->inject('logger', $container->logger);

                    $container->logger->debug("Service start.");

                    return $service;
                }
            };
        } else {
            $this->coreContainer->service = function () {};
        }

        // Model
        if ($modelNamespace !== null) {
            $modelClassPath = $modelNamespace . "\\" . $modelClassName;
            $this->coreContainer->model = function () use ($container, $classLoader, $modelClassPath, $modelClassName) {
                if ($classLoader->import($container->applicationInfo->applicationDir . "/models/" . $modelClassName . ".php")) {
                    $model = new $modelClassPath();
                    $model->inject('logger', $container->logger);

                    $container->logger->debug("Model start.");

                    return $model;
                }
            };
        } else {
            $classpath = "\WebStream\Exception\Extend\ClassNotFoundException";
            $message = $pageName . "Service and " . $pageName . "Model is not defined.";
            $this->coreContainer->model = new CoreExceptionDelegator($classpath, $message);
        }

        // Helper
        if ($helperNamespace !== null) {
            $helperClassPath = $helperNamespace . "\\" . $helperClassName;
            $this->coreContainer->helper = function () use ($container, $classLoader, $helperClassPath, $helperClassName) {
                if ($classLoader->import($container->applicationInfo->applicationDir . "/helpers/" . $helperClassName . ".php")) {
                    return new $helperClassPath($container);
                }
            };
        } else {
            $classpath = "\WebStream\Exception\Extend\ClassNotFoundException";
            $message = $pageName . "Helper is not defined.";
            $this->coreContainer->helper = new CoreExceptionDelegator($classpath, $message);
        }
    }

    /**
     * 名前空間を返却する
     * @param string アプリケーションルート
     * @param string クラス名
     * @return string 名前空間
     */
    public function getNamespace($appRoot, $className)
    {
        if (file_exists($appRoot) && is_dir($appRoot)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($appRoot),
                \RecursiveIteratorIterator::LEAVES_ONLY,
                \RecursiveIteratorIterator::CATCH_GET_CHILD // for Permission deny
            );
            foreach ($iterator as $filepath => $fileObject) {
                if (strpos($filepath, $className . ".php") !== false) {
                    return $this->getDefinedNamespace($filepath);
                }
            }
        }

        return null;
    }

    /**
     * ページ名を返却する
     * @return string ページ名
     */
    public function getPageName()
    {
        return $this->container->router->pageName;
    }

    /**
     * Controllerインスタンスを返却する
     * @return object Controllerインスタンス
     */
    public function getController()
    {
        return $this->coreContainer->controller;
    }

    /**
     * Viewインスタンスを返却する
     * @return object Viewインスタンス
     */
    public function getView()
    {
        return $this->coreContainer->view;
    }

    /**
     * Serviceインスタンスを返却する
     * @return object Serviceインスタンス
     */
    public function getService()
    {
        return $this->coreContainer->service;
    }

    /**
     * Modelインスタンスを返却する
     * @return object Modelインスタンス
     */
    public function getModel()
    {
        return $this->coreContainer->model;
    }

    /**
     * Helperインスタンスを返却する
     * @return object Helperインスタンス
     */
    public function getHelper()
    {
        return $this->coreContainer->helper;
    }
}
