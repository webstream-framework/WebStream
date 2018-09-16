<?php
namespace WebStream\Delegate;

use WebStream\Core\CoreInterface;
use WebStream\Core\CoreController;
use WebStream\Core\CoreService;
use WebStream\Core\CoreModel;
use WebStream\Core\CoreView;
use WebStream\Core\CoreHelper;
use WebStream\Container\Container;
use WebStream\Annotation\Attributes\Alias;
use WebStream\Annotation\Attributes\Database;
use WebStream\Annotation\Attributes\ExceptionHandler;
use WebStream\Annotation\Attributes\Filter;
use WebStream\Annotation\Attributes\Header;
use WebStream\Annotation\Attributes\Query;
use WebStream\Annotation\Attributes\Template;
use WebStream\Annotation\Attributes\Validate;
use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Reader\AnnotationReader;
use WebStream\Annotation\Reader\Extend\FilterExtendReader;
use WebStream\Annotation\Reader\Extend\QueryExtendReader;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Template\Basic;
use WebStream\Template\Twig;

/**
 * AnnotationDelegator
 * @author Ryuichi TANAKA.
 * @since 2015/02/11
 * @version 0.4
 */
class AnnotationDelegator
{
    /**
     * @var Container 依存コンテナ
     */
    private $container;

    /**
     * @var Container アノテーションコンテナ
     */
    private $annotationContainer;

    /**
     * @var Logger ロガー
     */
    private $logger;

    /**
     * Constructor
     * @param CoreInterface インスタンス
     * @param Container DIContainer
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->annotationContainer = new Container(false);
        $this->logger = $container->logger;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->logger->debug("AnnotationDelegator container is clear.");
    }

    /**
     * アノテーション情報をロードする
     * @param object インスタンス
     * @param string メソッド
     * @return Container コンテナ
     */
    public function read($instance, $method = null)
    {
        if (!$instance instanceof IAnnotatable) {
            $this->logger->warn("Annotation is not available this class: " . get_class($instance));
            return;
        }

        $this->container->executeMethod = $method ?: "";
        $annotationContainer = null;

        if ($instance instanceof CoreController) {
            if ($this->annotationContainer->controller === null) {
                $this->annotationContainer->controller = $this->readController($instance);
            }
            $annotationContainer = $this->annotationContainer->controller;
        } elseif ($instance instanceof CoreService) {
            if ($this->annotationContainer->service === null) {
                $this->annotationContainer->service = $this->readService($instance);
            }
            $annotationContainer = $this->annotationContainer->service;
        } elseif ($instance instanceof CoreModel) {
            if ($this->annotationContainer->model === null) {
                $this->annotationContainer->model = $this->readModel($instance);
            }
            $annotationContainer = $this->annotationContainer->model;
        } elseif ($instance instanceof CoreView) {
            if ($this->annotationContainer->view === null) {
                $this->annotationContainer->view = $this->readView($instance);
            }
            $annotationContainer = $this->annotationContainer->view;
        } elseif ($instance instanceof CoreHelper) {
            if ($this->annotationContainer->helper === null) {
                $this->annotationContainer->helper = $this->readHelper($instance);
            }
            $annotationContainer = $this->annotationContainer->helper;
        }

        return $annotationContainer;
    }

    /**
     * Controllerのアノテーション情報をロードする
     * @param CoreController インスタンス
     * @return Container コンテナ
     */
    private function readController(CoreController $instance)
    {
        $reader = new AnnotationReader($instance);
        $reader->inject('defaultContainer', $this->container);
        $reader->setActionMethod($this->container->executeMethod);

        // @Header
        $container = new Container();
        $container->requestMethod = $this->container->request->requestMethod;
        $container->contentType = 'html';
        $container->logger = $this->container->logger;
        $reader->readable(Header::class, $container);

        // @Filter
        $container = new Container();
        $container->action = $this->container->executeMethod;
        $container->logger = $this->container->logger;
        $reader->readable(Filter::class, $container);
        $reader->useExtendReader(Filter::class, FilterExtendReader::class);

        // @Template
        $container = new Container();
        $container->action = $this->container->executeMethod;
        $container->engine = [
            'basic' => Basic::class,
            'twig' => Twig::class
        ];
        $container->logger = $this->container->logger;
        $reader->readable(Template::class, $container);

        // @Validate
        $container = new Container();
        $requestMethod = $this->container->request->requestMethod;
        $container->request = new Container(false);
        $container->request->requestMethod = $requestMethod;
        $requestMethod = mb_strtolower($requestMethod);
        $container->request->{$requestMethod} = $this->container->request->{$requestMethod};
        $container->applicationInfo = new Container(false);
        $container->applicationInfo = $this->container->applicationInfo;
        $container->logger = $this->container->logger;
        $reader->readable(Validate::class, $container);

        // @ExceptionHandler
        $container = new Container();
        $container->logger = $this->container->logger;
        $reader->readable(ExceptionHandler::class, $container);

        // @Alias
        $container = new Container();
        $container->action = $this->container->executeMethod;
        $container->logger = $this->container->logger;
        $reader->readable(Alias::class, $container);

        $reader->readMethod();

        $annotationContainer = new AnnotationContainer();
        $annotationContainer->annotationInfoList = $reader->getAnnotationInfoList();
        $annotationContainer->exception = $reader->getException();
        $annotationContainer->customAnnotationInfoList = array_filter($annotationContainer->annotationInfoList, function ($key) {
            return !in_array($key, [Filter::class, Header::class, ExceptionHandler::class, Template::class, Alias::class], true);
        }, ARRAY_FILTER_USE_KEY);

        return $annotationContainer;
    }

    /**
     * Serviceのアノテーション情報をロードする
     * @param CoreService インスタンス
     * @return Container コンテナ
     */
    private function readService(CoreService $instance)
    {
        $reader = new AnnotationReader($instance);
        $reader->setActionMethod($this->container->executeMethod);

        // @Filter
        $container = new Container();
        $container->action = $this->container->executeMethod;
        $container->logger = $this->container->logger;
        $reader->readable(Filter::class, $container);
        $reader->useExtendReader(Filter::class, FilterExtendReader::class);

        // @ExceptionHandler
        $container = new Container();
        $container->logger = $this->container->logger;
        $reader->readable(ExceptionHandler::class, $container);

        // @Alias
        $container = new Container();
        $container->action = $this->container->executeMethod;
        $container->logger = $this->container->logger;
        $reader->readable(Alias::class, $container);

        $reader->readMethod();

        $annotationContainer = new AnnotationContainer();
        $annotationContainer->annotationInfoList = $reader->getAnnotationInfoList();
        $annotationContainer->exception = $reader->getException();
        $annotationContainer->customAnnotationInfoList = array_filter($annotationContainer->annotationInfoList, function ($key) {
            return !in_array($key, [Filter::class, ExceptionHandler::class, Alias::class], true);
        }, ARRAY_FILTER_USE_KEY);


        return $annotationContainer;
    }

    /**
     * Modelのアノテーション情報をロードする
     * @param CoreModel インスタンス
     * @return Container コンテナ
     */
    private function readModel(CoreModel $instance)
    {
        $reader = new AnnotationReader($instance);
        $reader->setActionMethod($this->container->executeMethod);

        // @Filter
        $container = new Container();
        $container->action = $this->container->executeMethod;
        $container->logger = $this->container->logger;
        $reader->readable(Filter::class, $container);
        $reader->useExtendReader(Filter::class, FilterExtendReader::class);

        // @ExceptionHandler
        $container = new Container();
        $container->logger = $this->container->logger;
        $reader->readable(ExceptionHandler::class, $container);

        // @Database
        $container = new Container();
        $container->rootPath = $this->container->applicationInfo->applicationRoot;
        $container->logger = $this->container->logger;
        $reader->readable(Database::class, $container);

        // @Query
        $container = new Container();
        $container->rootPath = $this->container->applicationInfo->applicationRoot;
        $container->logger = $this->container->logger;
        $reader->readable(Query::class, $container);
        $reader->useExtendReader(Query::class, QueryExtendReader::class);

        // @Alias
        $container = new Container();
        $container->action = $this->container->executeMethod;
        $container->logger = $this->container->logger;
        $reader->readable(Alias::class, $container);

        $reader->readClass();
        $reader->readMethod();

        $annotationContainer = new AnnotationContainer();
        $annotationContainer->annotationInfoList = $reader->getAnnotationInfoList();
        $annotationContainer->exception = $reader->getException();
        $annotationContainer->customAnnotationInfoList = array_filter($annotationContainer->annotationInfoList, function ($key) {
            return !in_array($key, [Filter::class, ExceptionHandler::class, Database::class, Query::class, Alias::class], true);
        }, ARRAY_FILTER_USE_KEY);

        return $annotationContainer;
    }

    /**
     * Viewのアノテーション情報をロードする
     * @param CoreView インスタンス
     * @return Container コンテナ
     */
    private function readView(CoreView $instance)
    {
        $reader = new AnnotationReader($instance);

        // @Filter
        $container = new Container();
        $container->action = $this->container->executeMethod;
        $container->logger = $this->container->logger;
        $reader->readable(Filter::class, $container);
        $reader->useExtendReader(Filter::class, FilterExtendReader::class);

        $reader->readMethod();

        $annotationContainer = new AnnotationContainer();
        $annotationContainer->annotationInfoList = $reader->getAnnotationInfoList();
        $annotationContainer->exception = $reader->getException();

        return $annotationContainer;
    }

    /**
     * Helperのアノテーション情報をロードする
     * @param CoreHelper インスタンス
     * @return Container コンテナ
     */
    private function readHelper(CoreHelper $instance)
    {
        $reader = new AnnotationReader($instance);
        $reader->setActionMethod($this->container->executeMethod);

        // @Filter
        $container = new Container();
        $container->action = $this->container->executeMethod;
        $container->logger = $this->container->logger;
        $reader->readable(Filter::class, $container);
        $reader->useExtendReader(Filter::class, FilterExtendReader::class);

        // @ExceptionHandler
        $container = new Container();
        $container->logger = $this->container->logger;
        $reader->readable(ExceptionHandler::class, $container);

        // @Alias
        $container = new Container();
        $container->action = $this->container->executeMethod;
        $container->logger = $this->container->logger;
        $reader->readable(Alias::class, $container);

        $reader->readMethod();

        $annotationContainer = new AnnotationContainer();
        $annotationContainer->annotationInfoList = $reader->getAnnotationInfoList();
        $annotationContainer->exception = $reader->getException();
        $annotationContainer->customAnnotationInfoList = array_filter($annotationContainer->annotationInfoList, function ($key) {
            return !in_array($key, [Filter::class, ExceptionHandler::class, Alias::class], true);
        }, ARRAY_FILTER_USE_KEY);

        return $annotationContainer;
    }
}
