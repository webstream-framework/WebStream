<?php
namespace WebStream\Delegate;

use WebStream\Core\CoreController;
use WebStream\Module\Container;
use WebStream\Module\Cache;
use WebStream\Module\Utility;
use WebStream\Module\Logger;
use WebStream\Exception\ApplicationException;
use WebStream\Exception\Extend\RouterException;
use WebStream\Exception\Extend\ResourceNotFoundException;
use WebStream\Exception\Extend\ClassNotFoundException;
use WebStream\Exception\Extend\MethodNotFoundException;
use WebStream\Exception\Extend\AnnotationException;
use WebStream\Annotation\Reader\ExceptionHandlerReader;
use WebStream\Annotation\Reader\AnnotationReader;
use WebStream\Annotation\Reader\AutowiredReader;
use WebStream\Annotation\Reader\HeaderReader;
use WebStream\Annotation\Reader\FilterReader;
use WebStream\Annotation\Reader\TemplateReader;
use WebStream\Annotation\Reader\TemplateCacheReader;
use Doctrine\Common\Annotations\AnnotationException as DoctrineAnnotationException;

/**
 * Resolver
 * @author Ryuichi TANAKA.
 * @since 2012/12/22
 * @version 0.4
 */
class Resolver
{
    use Utility;

    /** ルーティングオブジェクト */
    private $router;
    /** リクエストオブジェクト */
    private $request;
    /** レスポンスオブジェクト */
    private $response;
    /** セッションオブジェクト */
    private $session;
    /** DIコンテナ */
    private $container;

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
     * Resolverを起動する
     */
    public function run()
    {
        // ルータインスタンスをセットする必要がある
        if (!$this->router instanceof Router) {
            throw new RouterException("Required router instance to start the Controller");
        }

        // ルーティング解決を実行
        $this->router->resolve();
        // セッションスタート
        $this->session->start();
        // バッファリング開始
        $this->response->start();

        if ($this->router->controller() !== null && $this->router->action() !== null) {
            $this->runController();
        } elseif ($this->router->staticFile() !== null) {
            $this->readFile();
        } else {
            $errorMsg = "Failed to resolve the routing: " . $this->request->server("REQUEST_URI");
            throw new ResourceNotFoundException($errorMsg);
        }

        $this->response->end();
    }

    /**
     * Controllerを起動する
     */
    private function runController()
    {
        // クラスパスを取得
        $coreDelegator = $this->container->coreDelegator;
        $controllerInstance = $coreDelegator->getController();
        $controller = $this->router->controller();
        $action = $this->router->action();
        $params = $this->router->params();

        if (!method_exists($controllerInstance, $action)) {
            $class = get_class($controllerInstance);
            throw new MethodNotFoundException("${class}#${action} is not defined.");
        }

        // バリデーションチェック
        $validator = $this->container->validator;
        $validator->check();

        // テンプレートキャッシュチェック
        $pageName = $coreDelegator->getPageName();
        $cacheFile = STREAM_CACHE_PREFIX . $this->camel2snake($pageName) . "-" . $this->camel2snake($action);
        $cache = new Cache(STREAM_APP_ROOT . "/app/views/" . STREAM_VIEW_CACHE);
        $data = $cache->get($cacheFile);

        if ($data !== null) {
            echo $data;

            return;
        }

        try {
            $iterator = $this->getFileSearchIterator(STREAM_APP_ROOT . "/app/controllers");
            foreach ($iterator as $filepath => $fileObject) {
                if (strpos($filepath, $controller . ".php") !== false) {
                    include_once $filepath;
                }
            }

            // Controller起動
            $refClass = new \ReflectionClass($controllerInstance);

            // AnnotaionReaderを取得
            $reader = new AnnotationReader($controllerInstance);
            $this->container->classpath = $refClass->getName();
            $this->container->action = $action;
            $reader->setContainer($this->container);
            $reader->read();

            // @Autowired
            $autowired = new AutowiredReader($reader);
            $autowired->inject($controllerInstance);

            // @Header
            $header = new HeaderReader($reader);
            $headerContainer = $header->read();
            $mimeType = $headerContainer->contentType ?: "html";

            // @Filter
            $filter = new FilterReader($reader);
            $filterContainer = $filter->read();

            // initialize filter
            foreach ($filterContainer->initialize as $refMethod) {
                $refMethod->invoke($controllerInstance);
            }

            // before filter
            foreach ($filterContainer->before as $refMethod) {
                $refMethod->invoke($controllerInstance);
            }

            // action
            $controllerInstance->{$action}($params);

            // @Template
            $template = new TemplateReader($reader);
            $templateContainer = $template->read();

            $pageName = $coreDelegator->getPageName();
            $viewParams = [];
            $viewParams["model"] = $controllerInstance->__model();
            $viewParams["helper"] = $coreDelegator->getHelper();

            if ($templateContainer->base !== null) {
                $viewParams["base"] = $templateContainer->base;
            }
            if ($templateContainer->parts !== null) {
                foreach ($templateContainer->parts as $key => $value) {
                    $viewParams[$key] = $value;
                }
            }

            // draw template
            $view = $coreDelegator->getView();
            $view->draw($templateContainer->base, $viewParams, $mimeType);

            $templateCache = new TemplateCacheReader($reader);
            $templateCacheContainer = $templateCache->read();
            $expire = $templateCacheContainer->expire;

            if ($expire !== null) {
                // create cache
                $cacheFile = STREAM_CACHE_PREFIX . $this->camel2snake($coreDelegator->getPageName()) . "-" . $this->camel2snake($action);
                $view->cache($cacheFile, ob_get_contents(), $expire);
            }

            // after filter
            foreach ($filterContainer->after as $refMethod) {
                $refMethod->invoke($controllerInstance);
            }

        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e);
        } catch (\ReflectionException $e) {
            throw new ClassNotFoundException($e);
        }
    }

    /**
     * ファイルを読み込む
     */
    private function readFile()
    {
        $controller = new CoreController($this->container);
        $controller->__callStaticFile($this->router->staticFile());
    }

    /**
     * 指定したステータスコードのページに遷移する
     * @param Integer ステータスコード
     */
    public function move($statusCode)
    {
        $this->response->move($statusCode);
    }

    /**
     * エラー処理のハンドリングチェック
     * @param object エラーオブジェクト
     * @param array エラー内容
     * @return boolean ハンドリングするかどうか
     */
    public function handle(\Exception $e)
    {
        if ($this->router->controller() === null) {
            Logger::debug("Execution of handling is failure for static file.");

            return false;
        }

        $namespace = "";
        $iterator = $this->getFileSearchIterator(STREAM_APP_ROOT . "/app/controllers");
        foreach ($iterator as $filepath => $fileObject) {
            if (strpos($filepath, $this->router->controller() . ".php") !== false) {
                include_once $filepath;
                $namespace = $this->getNamespace($filepath);
                break;
            }
        }
        $classpath = $namespace . '\\' . $this->router->controller();

        if (!class_exists($classpath)) {
            return false;
        }

        $validator = $this->container->validator;
        $errorInfo = [
            "class" => $classpath,
            "method" => $this->router->action()
        ];

        try {
            // Controller起動
            $controllerInstance = $this->container->coreDelegator->getController();
            $refClass = new \ReflectionClass($controllerInstance);

            // @ExceptionHandlerを起動
            $reader = new AnnotationReader($controllerInstance);
            $reader->setContainer($this->container);
            $reader->read();

            $isHandled = false;
            $exceptionHandler = new ExceptionHandlerReader($reader);
            $exceptionHandlerContainer = $exceptionHandler->read();

            while ($refClass !== false) {
                if (array_key_exists($refClass->getName(), $exceptionHandlerContainer->exceptions)) {
                    $exceptionMethods = $exceptionHandlerContainer->exceptions[$refClass->getName()];
                    foreach ($exceptionMethods as $handleMethod => $exceptionMethodAnnotationClasses) {
                        foreach ($exceptionMethodAnnotationClasses as $exceptionClasses) {
                            foreach ($exceptionClasses as $exceptionClass) {
                                if (is_a($e, $exceptionClass)) {
                                    $ca = $classpath . "#" . $handleMethod;
                                    $instance = $refClass->newInstance($this->container);
                                    $method = $refClass->getMethod($handleMethod);
                                    $method->invokeArgs($instance, [$errorInfo]);
                                    $isHandled = true;
                                    Logger::debug("Execution of handling is success: " . $ca);
                                }
                            }
                        }
                    }

                }

                $refClass = $refClass->getParentClass();
            }

            return $isHandled;

        } catch (DoctrineAnnotationException $e) {
            Logger::error("Error occued in handled method: " . $ca);
            throw new AnnotationException($e);
        } catch (\ReflectionException $e) {
            Logger::error("Error occued in handled method: " . $ca);
            throw new ApplicationException($e);
        }

        return false;
    }
}
