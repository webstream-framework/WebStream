<?php
namespace WebStream\Delegate;

use WebStream\Core\CoreController;
use WebStream\Core\CoreService;
use WebStream\Core\CoreModel;
use WebStream\Core\CoreHelper;
use WebStream\Container\Container;
use WebStream\IO\File;
use WebStream\Exception\Extend\RouterException;
use WebStream\Exception\Extend\ResourceNotFoundException;

/**
 * Resolver
 * @author Ryuichi TANAKA.
 * @since 2012/12/22
 * @version 0.7
 */
class Resolver
{
    /**
     * @var Router ルーティングオブジェクト
     */
    private $router;

    /**
     * @var Request リクエストオブジェクト
     */
    private $request;

    /**
     * @var Response レスポンスオブジェクト
     */
    private $response;

    /**
     * @var Session セッションオブジェクト
     */
    private $session;

    /**
     * @var Container DIコンテナ
     */
    private $container;

    /**
     * @var AnnotationContainer アノテーションコンテナ
     */
    private $annotation;

    /**
     * コンストラクタ
     * @param Object DIコンテナ
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->request   = $container->request;
        $this->response  = $container->response;
        $this->session   = $container->session;
        $this->router    = $container->router;
    }

    /**
     * Controllerを起動する
     */
    public function runController()
    {
        // セッションスタート
        $this->session->start();
        // バッファリング開始
        $this->response->start();

        if ($this->router->controller !== null && $this->router->action !== null) {
            $iterator = [];
            $file = new File($this->container->applicationInfo->applicationRoot . "/app/controllers");
            if ($file->exists() && $file->isDirectory()) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($file->getFilePath()),
                    \RecursiveIteratorIterator::LEAVES_ONLY,
                    \RecursiveIteratorIterator::CATCH_GET_CHILD // for Permission deny
                );
            }
            foreach ($iterator as $filepath => $fileObject) {
                if (strpos($filepath, $this->router->controller . ".php") !== false) {
                    include_once $filepath;
                }
            }
            $controllerDelegator = new CoreExecuteDelegator($this->container->coreDelegator->getController(), $this->container);
            $controllerDelegator->run($this->router->action, [$this->router->params]);
        } elseif ($this->router->staticFile !== null) {
            $controller = new CoreController();
            $controller->inject('coreDelegator', $this->container->coreDelegator)
                       ->inject('logger', $this->container->logger);

            $controller->__callStaticFile($this->router->staticFile);
        } else {
            $this->response->clean();
            $errorMsg = "Failed to resolve the routing: " . $this->request->requestUri;
            throw new ResourceNotFoundException($errorMsg);
        }

        $this->response->end();
    }

    /**
     * Serviceを起動する
     * @return CoreService Serviceオブジェクト
     */
    public function runService()
    {
        $service = $this->container->coreDelegator->getService();
        $service = $service instanceof CoreService ? new CoreExecuteDelegator($service, $this->container) : $this->runModel();

        return $service;
    }

    /**
     * Modelを起動する
     * @return CoreModel Modelオブジェクト
     */
    public function runModel()
    {
        $model = $this->container->coreDelegator->getModel();
        $model = $model instanceof CoreModel ? new CoreExecuteDelegator($model, $this->container) : $model;

        return $model;
    }

    /**
     * Viewを起動する
     * @return CoreView Viewオブジェクト
     */
    public function runView()
    {
        return new CoreExecuteDelegator($this->container->coreDelegator->getView(), $this->container);
    }

    /**
     * Helperを起動する
     * @return CoreHelper Helperオブジェクト
     */
    public function runHelper()
    {
        $helper = $this->container->coreDelegator->getHelper();
        $helper = $helper instanceof CoreHelper ? new CoreExecuteDelegator($helper, $this->container) : $helper;

        return $helper;
    }
}
