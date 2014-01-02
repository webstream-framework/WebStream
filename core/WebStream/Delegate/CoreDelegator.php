<?php
namespace WebStream\Delegate;

use WebStream\Core\CoreView;
use WebStream\Module\Utility;
use WebStream\Module\Logger;
use WebStream\Module\Container;
use WebStream\Module\ClassLoader;
use WebStream\Annotation\DatabaseReader;

/**
 * CoreDelegator
 * @author Ryuichi TANAKA.
 * @since 2011/11/30
 * @version 0.4
 */
class CoreDelegator
{
    use Utility
    {
        Utility::getNamespace as getDefinedNamespace;
    }

    /** DIコンテナ */
    private $container;

    /** CoreContainer */
    private $coreContainer;

    /** Routerオブジェクト */
    private $router;

    /**
     * Constructor
     * @param object DIContainer
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->coreContainer = new Container();
        $this->router = $container->router;
        $this->initialize();
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->coreContainer->remove("view");
        $this->coreContainer->remove("service");
        $this->coreContainer->remove("model");
        $this->coreContainer->remove("helper");
        Logger::debug("CoreDelegator container is clear.");
    }

    /**
     * 各レイヤのオブジェクトをコンテナに設定する
     */
    private function initialize()
    {
        $classLoader = new ClassLoader();
        $container = $this->container;
        $pageName = $this->getPageName();

        $serviceClassName = $pageName . "Service";
        $serviceClassPath = $this->getNamespace($serviceClassName) . "\\" . $serviceClassName;
        $modelClassName   = $pageName . "Model";
        $modelClassPath   = $this->getNamespace($modelClassName) . "\\" . $modelClassName;
        $helperClassName  = $pageName . "Helper";
        $helperClassPath  = $this->getNamespace($helperClassName) . "\\" . $helperClassName;

        // View
        $this->coreContainer->view = function() use (&$container) {
            return new CoreView($container);
        };
        // Service
        $this->coreContainer->service = function() use (&$container, &$classLoader, &$serviceClassPath, &$serviceClassName) {
            if ($classLoader->import(STREAM_APP_DIR . "/services/" . $serviceClassName . ".php")) {
                \WebStream\Module\Logger::debug($serviceClassPath);

                return new $serviceClassPath($container);
            }
        };
        // Model
        $this->coreContainer->model = function() use (&$container, &$classLoader, &$modelClassPath, &$modelClassName) {
            if ($classLoader->import(STREAM_APP_DIR . "/models/" . $modelClassName . ".php")) {
                \WebStream\Module\Logger::debug($modelClassPath);
                $refClass = new \ReflectionClass($modelClassPath);
                $reader = new DatabaseReader();
                $reader->read($refClass, null, $container);

                return $reader->getInstance();
            }
        };
        // Helper
        $this->coreContainer->helper = function() use (&$container, &$classLoader, &$helperClassPath, &$helperClassName) {
            if ($classLoader->import(STREAM_APP_DIR . "/helpers/" . $helperClassName . ".php")) {
                \WebStream\Module\Logger::debug($helperClassPath);

                return new $helperClassPath($container);
            }
        };
    }

    /**
     * 名前空間を返却する
     * @param string クラス名
     * @return string 名前空間
     */
    public function getNamespace($className)
    {
        // TODO ファイル検索がクソ重いので直す
        $filepathList = $this->fileSearch($className);
        $filepath = array_shift($filepathList);

        return $this->getDefinedNamespace($filepath);
    }

    /**
     * ページ名を返却する
     * @return string ページ名
     */
    public function getPageName()
    {
        $params = $this->router->routingParams();

        return $this->snake2ucamel($params['controller']);
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
