<?php
namespace WebStream\Delegate;

use WebStream\Core\CoreController;
use WebStream\Module\Container;
use WebStream\Module\Cache;
use WebStream\Module\Utility;
use WebStream\Module\Logger;
use WebStream\Exception\RouterException;
use WebStream\Exception\ResourceNotFoundException;
use WebStream\Exception\ClassNotFoundException;
use WebStream\Exception\AnnotationException;
use WebStream\Exception\ApplicationException;
use WebStream\Exception\MethodNotFoundException;
use WebStream\Exception\CollectionException;
use Doctrine\Common\Annotations\AnnotationException as DoctrineAnnotationException;
use WebStream\Annotation\ExceptionHandlerReader;
use WebStream\Annotation\FilterReader;
use WebStream\Annotation\AutowiredReader;
use WebStream\Annotation\TemplateReader;
use WebStream\Annotation\HeaderReader;
use WebStream\Annotation\TemplateCacheReader;

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
        $namespace = $coreDelegator->getNamespace($this->router->controller());
        $classpath = $namespace . "\\" . $this->router->controller();

        // バリデーションチェック
        $validator = $this->container->validator;
        $validator->check();

        // テンプレートキャッシュチェック
        $pageName = preg_replace("/Controller/", "", $this->router->controller());
        $cacheFile = STREAM_CACHE_PREFIX . $this->camel2snake($pageName) . "-" . $this->camel2snake($this->router->action());
        $cache = new Cache(STREAM_APP_ROOT . "/app/views/" . STREAM_VIEW_CACHE);
        $data = $cache->get($cacheFile);

        if ($data !== null) {
            echo $data;

            return;
        }

        $action = $this->router->action();
        $params = $this->router->params();

        try {
            $iterator = $this->getFileSearchIterator(STREAM_APP_ROOT . "/app/controllers");
            foreach ($iterator as $filepath => $fileObject) {
                if (strpos($filepath, $this->router->controller() . ".php") !== false) {
                    include_once $filepath;
                }
            }

            // Controller起動
            $refClass = new \ReflectionClass(new $classpath($this->container));

            // autowired
            $autowired = new AutowiredReader();
            $autowired->read($refClass, null, $this->container);
            $self = $autowired->getInstance();

            // header
            $header = new HeaderReader();
            $header->read($refClass, $action, $this->container);
            $mime = $header->getMimeType();

            // filter
            $reader = new FilterReader($self);
            $reader->read($refClass, $action);
            $filter = $reader->getComponent();

            // initialize filter
            $filter->initialize();
            // before filter
            $filter->before();

            // action
            $template = new TemplateReader();
            $template->read($refClass, $action, $this->container);
            $templateComponent = $template->getComponent();

            if (!method_exists($self, $action)) {
                $class = get_class($self);
                throw new MethodNotFoundException("${class}#${action} is not defined.");
            }

            $data = $self->{$action}($params);
            if ($data === null) {
                $data = [];
            }

            $embed = $templateComponent->getEmbed();
            if (!empty($embed)) {
                $data = array_merge($data, $embed);
            }

            // draw template
            $viewDir = STREAM_APP_ROOT . "/app/views";
            $view = $coreDelegator->getView();
            $view->draw($viewDir . "/" . $templateComponent->getBase(), $data, $mime);

            $templateCache = new TemplateCacheReader();
            $templateCache->read($refClass, $action);
            $expire = $templateCache->getExpire();

            if ($expire !== null) {
                // create cache
                $pageName = $coreDelegator->getPageName();
                $cacheFile = STREAM_CACHE_PREFIX . $this->camel2snake($pageName) . "-" . $this->camel2snake($action);
                $view->cache($cacheFile, ob_get_contents(), $expire);
            }

            // after filter
            $filter->after();
        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e->getMessage());
        } catch (CollectionException $e) {
            throw new ApplicationException($e);
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
        $ca = $classpath;
        $validator = $this->container->validator;
        $errorInfo = [
            "class" => $classpath,
            "method" => $this->router->action()
        ];

        try {
            // Controller起動
            $refClass = new \ReflectionClass(new $classpath($this->container));
            // @ExceptionHandlerを起動
            $reader = new ExceptionHandlerReader();
            $reader->setHandledException($e);
            $reader->read($refClass, null, $this->container);
            $handleMethods = $reader->getHandleMethods();

            if (count($handleMethods) === 0) {
                return false;
            }

            foreach ($handleMethods as $handleMethod) {
                $ca = $classpath . "#" . $handleMethod;
                $instance = $refClass->newInstance($this->container);
                $method = $refClass->getMethod($handleMethod);
                $method->invokeArgs($instance, [$errorInfo]);
                Logger::debug("Execution of handling is success: " . $ca);
            }

            return true;
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
