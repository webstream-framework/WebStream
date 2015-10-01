<?php
namespace WebStream\Delegate;

use WebStream\Core\CoreInterface;
use WebStream\Core\CoreController;
use WebStream\Core\CoreService;
use WebStream\Core\CoreModel;
use WebStream\Core\CoreView;
use WebStream\Core\CoreHelper;
use WebStream\Module\Logger;
use WebStream\Module\Container;
use WebStream\Annotation\Reader\AnnotationReader;
use WebStream\Annotation\Container\AnnotationContainer;

/**
 * AnnotationDelegator
 * @author Ryuichi TANAKA.
 * @since 2015/02/11
 * @version 0.4
 */
class AnnotationDelegator
{
    /**
     * @var Container コンテナ
     */
    private $container;

    /**
     * Constructor
     * @param CoreInterface インスタンス
     * @param Container DIContainer
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        Logger::debug("AnnotationDelegator container is clear.");
    }

    /**
     * アノテーション情報をロードする
     * @param CoreInterface インスタンス
     * @param string メソッド
     * @return Container コンテナ
     */
    public function read(CoreInterface $instance, $method)
    {
        $this->container->executeMethod = $method;

        if ($instance instanceof CoreController) {
            return $this->readController($instance);
        } elseif ($instance instanceof CoreService) {
            return $this->readService($instance);
        } elseif ($instance instanceof CoreModel) {
            return $this->readModel($instance);
        } elseif ($instance instanceof CoreView) {
            return $this->readView($instance);
        } elseif ($instance instanceof CoreHelper) {
            return $this->readHelper($instance);
        }
    }

    /**
     * Controllerのアノテーション情報をロードする
     * @param CoreController インスタンス
     * @return Container コンテナ
     */
    private function readController(CoreController $instance)
    {
        $container = $this->container;
        $reader = new AnnotationReader($instance, $container);
        $reader->read();
        $injectedAnnotation = $reader->getInjectedAnnotationInfo();

        $factory = new AnnotationDelegatorFactory($injectedAnnotation, $container);
        $annotationContainer = new AnnotationContainer();

        // exceptions
        $annotationContainer->exception = $reader->getException();

        // @Header
        $annotationContainer->header = $factory->createHeader();

        // @Filter
        $annotationContainer->filter = $factory->createFilter();

        // @Template
        $annotationContainer->template = $factory->createTemplate();

        // @ExceptionHandler
        $annotationContainer->exceptionHandler = $factory->createExceptionHandler();

        // custom annotation
        $annotationContainer->customAnnotations = $factory->createCustomAnnotation();

        return $annotationContainer;
    }

    /**
     * Serviceのアノテーション情報をロードする
     * @param CoreService インスタンス
     * @return Container コンテナ
     */
    private function readService(CoreService $instance)
    {
        $container = $this->container;
        $reader = new AnnotationReader($instance, $container);
        $reader->read();
        $injectedAnnotation = $reader->getInjectedAnnotationInfo();

        $factory = new AnnotationDelegatorFactory($injectedAnnotation, $container);
        $annotationContainer = new AnnotationContainer();

        // exceptions
        $annotationContainer->exception = $reader->getException();

        // @Filter
        $annotationContainer->filter = $factory->createFilter();

        // @ExceptionHandler
        $annotationContainer->exceptionHandler = $factory->createExceptionHandler();

        // custom annotation
        $annotationContainer->customAnnotations = $factory->createCustomAnnotation();

        return $annotationContainer;
    }

    /**
     * Modelのアノテーション情報をロードする
     * @param CoreModel インスタンス
     * @return Container コンテナ
     */
    private function readModel(CoreModel $instance)
    {
        $container = $this->container;
        $reader = new AnnotationReader($instance, $container);
        $reader->read();
        $injectedAnnotation = $reader->getInjectedAnnotationInfo();

        $factory = new AnnotationDelegatorFactory($injectedAnnotation, $container);
        $annotationContainer = new AnnotationContainer();

        // exceptions
        $annotationContainer->exception = $reader->getException();

        // @Filter
        $annotationContainer->filter = $factory->createFilter();

        // @ExceptionHandler
        $annotationContainer->exceptionHandler = $factory->createExceptionHandler();

        // @Database
        $annotationContainer->database = $factory->createDatabase();

        // @Query
        $annotationContainer->query = $factory->createQuery();

        // custom annotation
        $annotationContainer->customAnnotations = $factory->createCustomAnnotation();

        return $annotationContainer;
    }

    /**
     * Viewのアノテーション情報をロードする
     * @param CoreView インスタンス
     * @return Container コンテナ
     */
    private function readView(CoreView $instance)
    {
        $container = $this->container;
        $reader = new AnnotationReader($instance, $container);
        $reader->read();
        $injectedAnnotation = $reader->getInjectedAnnotationInfo();

        $factory = new AnnotationDelegatorFactory($injectedAnnotation, $container);
        $annotationContainer = new AnnotationContainer();

        // exceptions
        $annotationContainer->exception = $reader->getException();

        // @Filter
        $annotationContainer->filter = $factory->createFilter();

        return $annotationContainer;
    }

    /**
     * Helperのアノテーション情報をロードする
     * @param CoreHelper インスタンス
     * @return Container コンテナ
     */
    private function readHelper(CoreHelper $instance)
    {
        $container = $this->container;
        $reader = new AnnotationReader($instance, $container);
        $reader->read();
        $injectedAnnotation = $reader->getInjectedAnnotationInfo();

        $factory = new AnnotationDelegatorFactory($injectedAnnotation, $container);
        $annotationContainer = new AnnotationContainer();

        // exceptions
        $annotationContainer->exception = $reader->getException();

        // @Filter
        $annotationContainer->filter = $factory->createFilter();

        // @ExceptionHandler
        $annotationContainer->exceptionHandler = $factory->createExceptionHandler();

        // custom annotation
        $annotationContainer->customAnnotations = $factory->createCustomAnnotation();

        return $annotationContainer;
    }
}
