<?php
namespace WebStream\Core;

use WebStream\Module\Container;
use WebStream\Module\ClassLoader;
use WebStream\Module\Utility;

/**
 * CoreBaseクラス
 * @author Ryuichi TANAKA.
 * @since 2012/12/27
 * @version 0.4
 */
class CoreBase
{
    use Utility;

    /** ページ名 */
    protected $__pageName;
    /** レイヤ名 */
    protected $__layerName;
    /** DIコンテナ */
    private $container;

    /**  各レイヤが呼び出し可能なレイヤルールを定義 */
    protected $__getClassRule = [
        'Controller' => ['Service', 'Model', 'View'],
        'Service'    => ['Model'],
        'Model'      => null,
        'View'       => ['Helper'],
        'Helper'     => ['View']
    ];

    /**
     * コンストラクタ
     * @param Object DIコンテナ
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->__pageName();
    }

    /**
     * ページ名をセットする
     * @param String ページ名
     */
    public function __pageName($pageName = null)
    {
        $this->__pageName = $this->__page() ?: $pageName;
    }

    /**
     * クラス名を返却する
     * @return String クラス名
     */
    public function __toString()
    {
        return get_class($this);
    }

    /**
     * ページ名を返却する
     * @return String ページ名
     */
    protected function __page()
    {
        $pageName = null;
        $classpath = explode('\\', $this->__toString());
        $className = end($classpath);
        if (preg_match('/^(.*)((?:Controll|Help)er|Service|Model|View)$/', $className, $matches)) {
            $this->__layerName = $matches[2];
            // Core(.*)クラスの場合ページ名は設定しない
            if ($matches[1] !== 'Core') {
                $pageName = $matches[1];
            }
        }

        return $pageName;
    }

    /**
     * 許可されたレイヤかどうか判定する
     * @return Boolean 許可レイヤかどうか
     */
    protected function __isPermitLayer($toLayer)
    {
        $list = null;
        if (array_key_exists($this->__layerName, $this->__getClassRule)) {
            $list = $this->__getClassRule[$this->__layerName];
        }

        return $list !== null && in_array($toLayer, $list);
    }

    /**
     * Controllerオブジェクトを返却する
     * @return Object Controllerオブジェクト
     */
    protected function __getController()
    {
        $classLoader = new ClassLoader();
        $className = $this->__pageName . 'Controller';
        if ($this->__isPermitLayer('Controller')  &&
            $classLoader->import(STREAM_APP_DIR . "/controllers/" . $className . ".php")) {
            // AppControllerは任意で使用可能
            $classLoader->import(STREAM_APP_DIR . "/controllers/AppController");
            $class = new \ReflectionClass(STREAM_CLASSPATH . $className);

            return $class->newInstance();
        }
    }

    /**
     * Serviceオブジェクトを返却する
     * @return Object Serviceオブジェクト
     */
    protected function __getService()
    {
        $className = $this->__pageName . 'Service';
        $classLoader = new ClassLoader();
        $filepath = $this->fileSearch($className);
        if (!empty($filepath)) {
            $namespace = $this->getNamespace($filepath[0]);

            // TODO

            if ($this->__isPermitLayer('Service') &&
                $classLoader->import(STREAM_APP_DIR . "/services/" . $className . ".php")) {
                $class = new \ReflectionClass($namespace . "\\" . $className);

                return $class->newInstance($this->container);
            }
        }
    }

    /**
     * Modelオブジェクトを返却する
     * @return Object Modelオブジェクト
     */
    protected function __getModel()
    {
        $className = $this->__pageName . 'Model';
        $classLoader = new ClassLoader();
        $filepath = $this->fileSearch($className);
        if (!empty($filepath)) {
            $namespace = $this->getNamespace($filepath[0]);
            if ($this->__isPermitLayer('Model') &&
                $classLoader->import(STREAM_APP_DIR . "/models/" . $className . ".php")) {
                $class = new \ReflectionClass($namespace . "\\" . $className);

                return $class->newInstance();
            }
        }
    }

    /**
     * Viewオブジェクトを返却する
     * @return Object Viewオブジェクト
     */
    protected function __getView()
    {
        if ($this->__isPermitLayer('View')) {
            $view = new CoreView($this->container);
            $view->__pageName($this->__pageName);

            return $view;
        }
    }

    /**
     * Helperオブジェクトを返却する
     * @return Object Helperオブジェクト
     */
    protected function __getHelper()
    {
        $className = $this->__pageName . 'Helper';
        if ($this->__isPermitLayer('Helper') &&
            import(STREAM_APP_DIR . "/helpers/" . $className)) {
            // AppHelperは任意で使用可能
            import(STREAM_APP_DIR . "/helpers/AppHelper");
            $class = new \ReflectionClass(STREAM_CLASSPATH . $className);

            return $class->newInstance($this->container);
        }
    }
}
