<?php
namespace WebStream;
/**
 * CoreBaseクラス
 * @author Ryuichi TANAKA.
 * @since 2012/12/27
 */
class CoreBase {
    /** ページ名 */
    protected $__pageName;
    /** レイヤ名 */
    protected $__layerName;

    /**  各レイヤが呼び出し可能なレイヤルールを定義 */
    protected $__getClassRule = array(
        'Controller' => array('Service', 'Model', 'View'),
        'Service'    => array('Model'),
        'Model'      => null,
        'View'       => array('Helper'),
        'Helper'     => array('View')
    );

    /**
     * コンストラクタ
     * @param String ページ名
     */
    public function __construct($pageName = null) {
        $this->__pageName = $this->__page() ?: $pageName;
    }

    /**
     * クラス名を返却する
     * @return String クラス名
     */
    public function __toString() {
        return get_class($this);
    }

    /**
     * ページ名を返却する
     * @return String ページ名
     */
    protected function __page() {
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
    protected function __isPermitLayer($toLayer) {
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
    protected function __getController() {
        $className = $this->__pageName . 'Controller';
        if ($this->__isPermitLayer('Controller')  &&
            import(STREAM_APP_DIR . "/controllers/" . $className)) {
            // AppControllerは任意で使用可能
            import(STREAM_APP_DIR . "/controllers/AppController");
            $class = new \ReflectionClass(STREAM_CLASSPATH . $className);
            return $class->newInstance();
        }
    }

    /**
     * Serviceオブジェクトを返却する
     * @return Object Serviceオブジェクト
     */
    protected function __getService() {
        $className = $this->__pageName . 'Service';
        if ($this->__isPermitLayer('Service') &&
            import(STREAM_APP_DIR . "/services/" . $className)) {
            $class = new \ReflectionClass(STREAM_CLASSPATH . $className);
            return $class->newInstance();
        }
    }

    /**
     * Modelオブジェクトを返却する
     * @return Object Modelオブジェクト
     */
    protected function __getModel() {
        $className = $this->__pageName . 'Model';
        if ($this->__isPermitLayer('Model') &&
            import(STREAM_APP_DIR . "/models/" . $className)) {
            $class = new \ReflectionClass(STREAM_CLASSPATH . $className);
            return $class->newInstance();
        }
    }

    /**
     * Viewオブジェクトを返却する
     * @return Object Viewオブジェクト
     */
    protected function __getView() {
        if ($this->__isPermitLayer('View')) {
            return new CoreView($this->__pageName);
        }
    }

    /**
     * Helperオブジェクトを返却する
     * @return Object Helperオブジェクト
     */
    protected function __getHelper() {
        $className = $this->__pageName . 'Helper';
        if ($this->__isPermitLayer('Helper') &&
            import(STREAM_APP_DIR . "/helpers/" . $className)) {
            // AppHelperは任意で使用可能
            import(STREAM_APP_DIR . "/helpers/AppHelper");
            $class = new \ReflectionClass(STREAM_CLASSPATH . $className);
            return $class->newInstance();
        }
    }
}