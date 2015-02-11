<?php
namespace WebStream\Delegate;

use WebStream\Core\CoreController;
use WebStream\Module\Container;
use WebStream\Module\Cache;
use WebStream\Module\Utility;
use WebStream\Module\Logger;
use WebStream\Annotation\Reader\AnnotationReader;
use WebStream\Exception\ApplicationException;
use WebStream\Exception\Extend\RouterException;
use WebStream\Exception\Extend\ResourceNotFoundException;
use WebStream\Exception\Extend\ClassNotFoundException;
use WebStream\Exception\Extend\MethodNotFoundException;
use WebStream\Exception\Extend\AnnotationException;
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
     * @var array<AnnotationContainer> 注入後アノテーション情報
     */
    private $injectedAnnotation;

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
            $reader = new AnnotationReader($controllerInstance, $this->container);
            $reader->read();
            $this->injectedAnnotation = $reader->getInjectedAnnotationInfo();

            // @Header
            $mimeType = "html";
            if (array_key_exists("WebStream\Annotation\Header", $this->injectedAnnotation)) {
                $headerAnnotations = $this->injectedAnnotation["WebStream\Annotation\Header"];
                $mimeType = $headerAnnotations[0]->contentType;
            }

            // @Filter
            $invokeInitializeList = [];
            $invokeBeforeList = [];
            $invokeAfterList = [];
            if (array_key_exists("WebStream\Annotation\Filter", $this->injectedAnnotation)) {
                $filterAnnotations = $this->injectedAnnotation["WebStream\Annotation\Filter"];
                foreach ($filterAnnotations as $filterAnnotation) {
                    if ($filterAnnotation->initialize !== null) {
                        $invokeInitializeList[] = $filterAnnotation->initialize;
                    }
                    if ($filterAnnotation->before !== null) {
                        $invokeBeforeList[] = $filterAnnotation->before;
                    }
                    if ($filterAnnotation->after !== null) {
                        $invokeAfterList[] = $filterAnnotation->after;
                    }
                }
            }

            // @Template
            $viewParams = [];
            $baseTemplate = null;
            if (array_key_exists("WebStream\Annotation\Template", $this->injectedAnnotation)) {
                $templateAnnotations = $this->injectedAnnotation["WebStream\Annotation\Template"];
                $baseTemplateCandidate = null;

                foreach ($templateAnnotations as $templateAnnotation) {
                    if ($baseTemplateCandidate === null) {
                        // ベーステンプレートは暫定的に1番はじめに指定されたテンプレートを設定する
                        $baseTemplateCandidate = $templateAnnotation->name;
                    }

                    if ($templateAnnotation->base !== null) {
                        if ($baseTemplate !== null) {
                            // ベーステンプレートが複数指定された場合、エラーとする
                            $errorMsg = "Invalid argument of @Template('" . $template . "') attribute 'type'.";
                            $errorMsg.= "The type attribute 'base' must be a only definition.";
                            throw new AnnotationException($errorMsg);
                        }
                        $baseTemplate = $templateAnnotation->base;
                    }

                    if ($templateAnnotation->parts !== null) {
                        foreach ($templateAnnotation->parts as $key => $value) {
                            $viewParams[$key] = $value;
                        }
                    }
                }
                if ($baseTemplate === null) {
                    $baseTemplate = $baseTemplateCandidate;
                }

                $viewParams["model"] = $coreDelegator->getService() ?: $coreDelegator->getModel();
                $viewParams["helper"] = $coreDelegator->getHelper();
            }

            // @Template
            $expire = null;
            if (array_key_exists("WebStream\Annotation\TemplateCache", $this->injectedAnnotation)) {
                $templateCacheAnnotations = $this->injectedAnnotation["WebStream\Annotation\TemplateCache"];
                $expire = $templateCacheAnnotations[0]->expire;
            }

            // initialize filter
            foreach ($invokeInitializeList as $refMethod) {
                $refMethod->invoke($controllerInstance);
            }

            // before filter
            foreach ($invokeBeforeList as $refMethod) {
                $refMethod->invoke($controllerInstance);
            }

            // action
            $controllerInstance->{$action}($params);

            // draw template
            $view = $coreDelegator->getView();
            $view->draw($baseTemplate, $viewParams, $mimeType);
            if ($expire !== null) {
                $cacheFile = STREAM_CACHE_PREFIX . $this->camel2snake($coreDelegator->getPageName()) . "-" . $this->camel2snake($action);
                $view->cache($cacheFile, ob_get_contents(), $expire);
            }

            // after filter
            foreach ($invokeAfterList as $refMethod) {
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

        $errorInfo = [
            "class" => $classpath,
            "method" => $this->router->action()
        ];

        try {
            // Controller起動
            $controllerInstance = $this->container->coreDelegator->getController();
            $exceptionHandlerAnnotations = $this->injectedAnnotation["WebStream\Annotation\ExceptionHandler"];

            $invokeMethods = [];
            foreach ($exceptionHandlerAnnotations as $exceptionHandlerAnnotation) {
                $exceptions = $exceptionHandlerAnnotation->exceptions;
                $refMethod = $exceptionHandlerAnnotation->method;
                foreach ($exceptions as $exception) {
                    if (is_a($e, $exception)) {
                        // 一つのメソッドに複数の捕捉例外が指定された場合(派生例外クラス含む)、先勝で1回のみ実行する
                        // そうでなければ複数回メソッドが実行されるため
                        // ただし同一クラス内に限る(親クラスの同一名のメソッドは実行する)
                        // TODO ここはテストを追加する
                        $key = $refMethod->class . "#" . $refMethod->name;
                        if (!array_key_exists($key, $invokeMethods)) {
                            $invokeMethods[$key] = $refMethod;
                        }
                    }
                }
            }

            foreach ($invokeMethods as $invokeMethod) {
                $invokeMethod->invokeArgs($controllerInstance, [$errorInfo]);
                $isHandled = true;
                Logger::debug("Execution of handling is success: " . $errorInfo["class"] . "#" . $errorInfo["method"]);
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
