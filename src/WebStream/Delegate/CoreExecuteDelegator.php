<?php
namespace WebStream\Delegate;

use WebStream\DI\Injector;
use WebStream\Core\CoreInterface;
use WebStream\Core\CoreController;
use WebStream\Core\CoreService;
use WebStream\Core\CoreModel;
use WebStream\Core\CoreView;
use WebStream\Core\CoreHelper;
use WebStream\Cache\Driver\CacheDriverFactory;
use WebStream\Container\Container;
use WebStream\Annotation\Attributes\Alias;
use WebStream\Annotation\Attributes\Database;
use WebStream\Annotation\Attributes\ExceptionHandler;
use WebStream\Annotation\Attributes\Filter;
use WebStream\Annotation\Attributes\Header;
use WebStream\Annotation\Attributes\Query;
use WebStream\Annotation\Attributes\Template;
use WebStream\Exception\ApplicationException;
use WebStream\Exception\SystemException;
use WebStream\Exception\DelegateException;
use WebStream\Exception\Delegate\ExceptionDelegator;
use WebStream\Exception\Extend\AnnotationException;
use WebStream\Exception\Extend\MethodNotFoundException;
use WebStream\Util\CommonUtils;
use Doctrine\Common\Annotations\AnnotationException as DoctrineAnnotationException;

/**
 * CoreExecuteDelegator
 * @author Ryuichi TANAKA.
 * @since 2015/02/25
 * @version 0.4
 */
class CoreExecuteDelegator
{
    use Injector, CommonUtils;

    /**
     * @var CoreInterface インスタンス
     */
    private $instance;

    /**
     * @var CoreInterface 注入済みインスタンス
     */
    private $injectedInstance;

    /**
     * @var Container 依存コンテナ
     */
    private $container;

    /**
     * @var Logger ロガー
     */
    private $logger;

    /**
     * @var AnnotationContainer アノテーション
     */
    private $annotation;

    /**
     * @var array<AnnotationContainer> 例外ハンドラリスト
     */
    private $exceptionHandler;

    /**
     * constructor
     */
    public function __construct(CoreInterface $instance, Container $container)
    {
        $this->instance = $instance;
        $this->container = $container;
        $this->logger = $container->logger;
    }

    /**
     * method missing
     */
    public function __call($method, $arguments)
    {
        return $this->run($method, $arguments);
    }

    /**
     * overload getter
     */
    public function __get($name)
    {
        return $this->getInstance()->{$name};
    }

    /**
     * 処理を実行する
     * @param string メソッド名
     * @param array 引数リスト
     */
    public function run($method, $arguments = [])
    {
        // すでに注入済みのインスタンスの場合、そのまま実行
        if ($this->injectedInstance !== null) {
            return $this->execute($method, $arguments);
        }

        try {
            $result = null;
            if ($this->instance instanceof CoreController) {
                $this->controllerInjector($this->getOriginMethod($method), $arguments);
            } elseif ($this->instance instanceof CoreService) {
                $result = $this->serviceInjector($this->getOriginMethod($method), $arguments);
            } elseif ($this->instance instanceof CoreModel) {
                $result = $this->modelInjector($this->getOriginMethod($method), $arguments);
            } elseif ($this->instance instanceof CoreView) {
                $this->viewInjector($method, $arguments);
            } elseif ($this->instance instanceof CoreHelper) {
                $result = $this->helperInjector($this->getOriginMethod($method), $arguments);
            }

            return $result;
        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e);
        } catch (DelegateException $e) {
            // すでにデリゲート済み例外の場合はそのままスロー
            // カスタムアノテーション定義で発生する
            throw $e;
        } catch (\Exception $e) {
            $exceptionClass = get_class($e);
            switch ($exceptionClass) {
                case "Exception":
                case "LogicException":
                    $e = new ApplicationException($e->getMessage(), 500, $e);
                    break;
                case "RuntimeException":
                    $e = new SystemException($e->getMessage(), 500, $e);
                    break;
            }

            $exception = new ExceptionDelegator($this->getInstance(), $e, $method);
            $exception->inject('logger', $this->logger);

            if ($this->exceptionHandler !== null) {
                $exception->setExceptionHandler($this->exceptionHandler);
            }

            $exception->raise();
        }
    }

    /**
     * オリジナルのインスタンスを返却する
     * @return CoreInterface インスタンス
     */
    public function getInstance()
    {
        return $this->injectedInstance ?: $this->instance;
    }

    /**
     * メソッドを実行する
     * @param string メソッド名
     * @param array 引数リスト
     */
    private function execute($method, $arguments)
    {
        // serviceの場合、modelの探索に行くためエラーにはしない
        if (!($this->injectedInstance instanceof CoreService) && !method_exists($this->injectedInstance, $method)) {
            $class = get_class($this->injectedInstance);
            throw new MethodNotFoundException("${class}#${method} is not defined.");
        }

        return call_user_func_array([$this->injectedInstance, $method], $arguments);
    }

    /**
     * Controllerに注入する
     * @param string メソッド名
     * @param array 引数リスト
     */
    private function controllerInjector($method, $arguments)
    {
        if (!method_exists($this->instance, $method)) {
            $this->injectedInstance = $this->instance;
            $class = get_class($this->instance);
            $this->instance = null;
            throw new MethodNotFoundException("${class}#${method} is not defined.");
        }

        $applicationInfo = $this->container->applicationInfo;

        // テンプレートキャッシュチェック
        $pageName = $this->container->coreDelegator->getPageName();
        $cacheFile = $applicationInfo->cachePrefix . $this->camel2snake($pageName) . "-" . $this->camel2snake($method);

        $factory = new CacheDriverFactory();
        $config = new Container(false);
        $config->cacheDir = $applicationInfo->applicationRoot . "/app/views/" . $applicationInfo->cacheDir;
        $config->classPrefix = "view_cache";
        $cache = $factory->create("WebStream\Cache\Driver\TemporaryFile", $config);
        $cache->inject('logger', $this->logger);
        $data = $cache->get($cacheFile);

        if ($data !== null) {
            $this->logger->debug("Template cache read success: $cacheFile.cache");
            echo $data;

            return;
        }

        $resolver = new Resolver($this->container);
        $model = $resolver->runService() ?: $resolver->runModel();

        // アノテーション注入処理は1度しか行わない
        if ($this->injectedInstance === null) {
            $annotation = $this->annotation = $this->container->annotationDelegator->read($this->instance, $method);
            $annotationClosure = function ($classpath) use ($annotation) {
                if (array_key_exists($classpath, $annotation->annotationInfoList)) {
                    return $annotation->annotationInfoList[$classpath];
                }
                return null;
            };

            // @Header
            $header = $annotationClosure(Header::class);

            // @Filter
            $filter = $annotationClosure(Filter::class);

            // @Template
            $template = $annotationClosure(Template::class);

            // @ExceptionHandler
            $this->exceptionHandler = $annotationClosure(ExceptionHandler::class);

            // custom annotation
            $this->instance->__customAnnotation($this->annotation->customAnnotationInfoList);

            // 各アノテーションでエラーがあった場合この時点で例外を起こす。
            // 例外発生を遅延実行させないとエラーになっていないアノテーション情報が取れない
            $exception = $annotation->exception;
            if ($exception instanceof ExceptionDelegator) {
                $exception->raise();
            }

            // initialize filter
            $container = $this->container;
            $container->model = $model;
            foreach ($filter->initialize as $refMethod) {
                $refMethod->invokeArgs($this->instance, [$container]);
            }

            // before filter
            foreach ($filter->before as $refMethod) {
                $refMethod->invoke($this->instance);
            }

            $this->injectedInstance = $this->instance;
            $this->execute($method, $arguments);

            // draw template
            if ($template !== null) {
                $mimeType = empty($header) ? "html" : $header[0]['contentType'];
                $refClass = new \ReflectionClass($template[0]['engine']);
                $templateEngine = $refClass->newInstance($this->container);

                $view = $resolver->runView();
                $view->setTemplateEngine($templateEngine);
                $view->draw([
                    'model' => $model,
                    'helper' => $resolver->runHelper(),
                    'mimeType' => $mimeType,
                    'filename' => $template[0]['filename']
                ]);

                if (array_key_exists('cacheTime', $template[0])) {
                    $cacheFile = $applicationInfo->cachePrefix . $this->camel2snake($pageName) . "-" . $this->camel2snake($method);
                    $view->templateCache($cacheFile, ob_get_contents(), $template[0]['cacheTime']);
                }
            }

            // after filter
            foreach ($filter->after as $refMethod) {
                $refMethod->invoke($this->injectedInstance);
            }

            $this->instance = null;
        }
    }

    /**
     * Serviceに注入する
     * @param string メソッド名
     * @param array 引数リスト
     */
    private function serviceInjector($method, $arguments)
    {
        // アノテーション注入処理は1度しか行わない
        if ($this->injectedInstance === null) {
            $annotation = $this->annotation = $this->container->annotationDelegator->read($this->instance, $method);
            $annotationClosure = function ($classpath) use ($annotation) {
                if (array_key_exists($classpath, $annotation->annotationInfoList)) {
                    return $annotation->annotationInfoList[$classpath];
                }
                return null;
            };

            // @Filter
            $filter = $annotationClosure(Filter::class);

            // @ExceptionHandler
            $exceptionHandler = $annotationClosure(ExceptionHandler::class);

            // custom annotation
            $this->instance->__customAnnotation($this->annotation->customAnnotationInfoList);

            // 各アノテーションでエラーがあった場合この時点で例外を起こす。
            // 例外発生を遅延実行させないとエラーになっていないアノテーション情報が取れない
            $exception = $annotation->exception;
            if ($exception instanceof ExceptionDelegator) {
                $this->exceptionHandler = $exceptionHandler;
                $exception->raise();
            }

            foreach ($filter->initialize as $refMethod) {
                $refMethod->invokeArgs($this->instance, [$this->container]);
            }

            $this->injectedInstance = $this->instance;
            $this->instance = null;
        }

        return $this->execute($method, $arguments);
    }

    /**
     * Modelに注入する
     * @param string メソッド名
     * @param array 引数リスト
     */
    private function modelInjector($method, $arguments)
    {
        // アノテーション注入処理は1度しか行わない
        if ($this->injectedInstance === null) {
            $annotation = $this->annotation = $this->container->annotationDelegator->read($this->instance, $method);
            $annotationClosure = function ($classpath) use ($annotation) {
                if (array_key_exists($classpath, $annotation->annotationInfoList)) {
                    return $annotation->annotationInfoList[$classpath];
                }
                return null;
            };

            // @Filter
            $filter = $annotationClosure(Filter::class);

            // @ExceptionHandler
            $exceptionHandler = $annotationClosure(ExceptionHandler::class);

            // @Database
            $database = $annotationClosure(Database::class);

            // @Query
            $query = $annotationClosure(Query::class);

            // custom annotation
            $this->instance->__customAnnotation($this->annotation->customAnnotationInfoList);

            // 各アノテーションでエラーがあった場合この時点で例外を起こす。
            // 例外発生を遅延実行させないとエラーになっていないアノテーション情報が取れない
            $exception = $annotation->exception;
            if ($exception instanceof ExceptionDelegator) {
                $this->exceptionHandler = $exceptionHandler;
                $exception->raise();
            }

            $connectionContainerList = [];
            foreach ($database as $databaseInfo) {
                $container = new Container(false);
                $container->filepath = $databaseInfo['filepath'];
                $container->configPath = $databaseInfo['configPath'];
                $container->driverClassPath = $databaseInfo['driverClassPath'];
                $connectionContainerList[] = $container;
            }

            $connectionContainer = new Container();
            $connectionContainer->connectionContainerList = $connectionContainerList;
            $connectionContainer->register("queryInfo", $query);
            $connectionContainer->logger = $this->logger;

            foreach ($filter->initialize as $refMethod) {
                $refMethod->invokeArgs($this->instance, [$connectionContainer]);
            }

            $this->injectedInstance = $this->instance;
            $this->instance = null;
        }

        return $this->execute($method, $arguments);
    }

    /**
     * Viewに注入する
     * @param string メソッド名
     * @param array 引数リスト
     */
    private function viewInjector($method, $arguments)
    {
        // アノテーション注入処理は1度しか行わない
        if ($this->injectedInstance === null) {
            $annotation = $this->annotation = $this->container->annotationDelegator->read($this->instance, $method);
            $annotationClosure = function ($classpath) use ($annotation) {
                if (array_key_exists($classpath, $annotation->annotationInfoList)) {
                    return $annotation->annotationInfoList[$classpath];
                }
                return null;
            };

            // 各アノテーションでエラーがあった場合この時点で例外を起こす。
            // 例外発生を遅延実行させないとエラーになっていないアノテーション情報が取れない
            $exception = $annotation->exception;
            if ($exception instanceof ExceptionDelegator) {
                $exception->raise();
            }

            $this->injectedInstance = $this->instance;
            $this->instance = null;
        }

        $this->execute($method, $arguments);
    }

    /**
     * Helperに注入する
     * @param string メソッド名
     * @param array 引数リスト
     */
    private function helperInjector($method, $arguments)
    {
        // アノテーション注入処理は1度しか行わない
        if ($this->injectedInstance === null) {
            $annotation = $this->annotation = $this->container->annotationDelegator->read($this->instance, $method);
            $annotationClosure = function ($classpath) use ($annotation) {
                if (array_key_exists($classpath, $annotation->annotationInfoList)) {
                    return $annotation->annotationInfoList[$classpath];
                }
                return null;
            };

            // @Filter
            $filter = $annotationClosure(Filter::class);

            // @ExceptionHandler
            $exceptionHandler = $annotationClosure(ExceptionHandler::class);

            // custom annotation
            $this->instance->__customAnnotation($this->annotation->customAnnotationInfoList);

            // 各アノテーションでエラーがあった場合この時点で例外を起こす。
            // 例外発生を遅延実行させないとエラーになっていないアノテーション情報が取れない
            $exception = $annotation->exception;
            if ($exception instanceof ExceptionDelegator) {
                $this->exceptionHandler = $exceptionHandler;
                $exception->raise();
            }

            foreach ($filter->initialize as $refMethod) {
                $refMethod->invokeArgs($this->instance, [$this->container]);
            }

            $this->injectedInstance = $this->instance;
            $this->instance = null;
        }

        return $this->execute($method, $arguments);
    }

    /**
     * 実メソッド名を返却する
     * @param  stirng $method エイリアスメソッド名
     * @return stirng 実メソッド名
     */
    private function getOriginMethod($method)
    {
        // 実メソッドが定義済みの場合、エイリアスメソッド参照はしない
        if (method_exists($this->instance, $method)) {
            return $method;
        }

        $annotation = $this->container->annotationDelegator->read($this->instance, $method);
        $annotationClosure = function ($classpath) use ($annotation) {
            if (array_key_exists($classpath, $annotation->annotationInfoList)) {
                return $annotation->annotationInfoList[$classpath];
            }
            return null;
        };

        $annotation = $annotationClosure(Alias::class);

        $originMethod = null;
        foreach ($annotation as $alias) {
            if ($originMethod !== null && $alias['method'] !== null) {
                throw new AnnotationException("Alias method of the same name is defined: $method");
            }
            if ($alias['method'] !== null) {
                $originMethod = $alias['method'];
            }
        }

        if ($originMethod !== null) {
            $class = get_class($this->instance);
            $this->logger->debug("Alias method found. Transfer from ${class}#${method} to ${class}#${originMethod}.");
        } else {
            $originMethod = $method;
        }

        return $originMethod;
    }
}
