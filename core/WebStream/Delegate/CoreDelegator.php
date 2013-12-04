<?php
namespace WebStream\Delegate;

use WebStream\Core\CoreView;
use WebStream\Module\Utility;
use WebStream\Module\Container;
use WebStream\Module\ClassLoader;

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

    /** Routerオブジェクト */
    private $router;

    /**
     * コンストラクタ
     * @param Router Routerオブジェクト
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->router = $container->router;
    }

    /**
     * 名前空間を返却する
     * @param string クラス名
     * @return string 名前空間
     */
    public function getNamespace($className)
    {
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
        return new CoreView($this->container);
    }

    /**
     * Serviceインスタンスを返却する
     * @return object Serviceインスタンス
     */
    public function getService()
    {
        $className = $this->getPageName() . "Service";
        $classpath = $this->getNamespace($className) . "\\" . $className;
        $classLoader = new ClassLoader();
        if ($classLoader->import(STREAM_APP_DIR . "/services/" . $className . ".php")) {
            \WebStream\Module\Logger::debug($classpath);
            $class = new \ReflectionClass($classpath);

            return $class->newInstance($this->container);
        }
    }

    /**
     * Modelインスタンスを返却する
     * @return object Modelインスタンス
     */
    public function getModel()
    {
        $className = $this->getPageName() . "Model";
        $classpath = $this->getNamespace($className) . "\\" . $className;
        $classLoader = new ClassLoader();
        if ($classLoader->import(STREAM_APP_DIR . "/models/" . $className . ".php")) {
            $class = new \ReflectionClass($classpath);

            return $class->newInstance($this->container);
        }
    }

    /**
     * Helperインスタンスを返却する
     * @return object Helperインスタンス
     */
    public function getHelper()
    {
        $className = $this->getPageName() . "Helper";
        $classpath = $this->getNamespace($className) . "\\" . $className;
        $classLoader = new ClassLoader();
        if ($classLoader->import(STREAM_APP_DIR . "/helpers/" . $className . ".php")) {
            $class = new \ReflectionClass($classpath);

            return $class->newInstance($this->container);
        }
    }
}
