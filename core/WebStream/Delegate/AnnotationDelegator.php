<?php
namespace WebStream\Delegate;

use WebStream\Core\CoreInterface;
use WebStream\Core\CoreController;
use WebStream\Core\CoreModel;
use WebStream\Module\Logger;
use WebStream\Module\Container;
use WebStream\Annotation\Reader\AnnotationReader;
use WebStream\Exception\Extend\AnnotationException;

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
     * @return Container コンテナ
     */
    public function read(CoreInterface $instance)
    {
        if ($instance instanceof CoreController) {
            return $this->readController($instance);
        } elseif ($instance instanceof CoreModel) {
            return $this->readModel($instance);
        }
    }

    /**
     * Controllerのアノテーション情報をロードする
     * @param CoreInterface インスタンス
     * @return Container コンテナ
     */
    private function readController(CoreController $instance)
    {
        $container = $this->container;
        $reader = new AnnotationReader($instance, $container);
        $reader->read();
        $injectedAnnotation = $reader->getInjectedAnnotationInfo();

        $annotationContainer = new Container();

        // @Header
        $annotationContainer->header = function () use ($injectedAnnotation) {
            $headerContainer = new Container();
            $headerContainer->mimeType = "html";
            if (array_key_exists("WebStream\Annotation\Header", $injectedAnnotation)) {
                $headerAnnotations = $injectedAnnotation["WebStream\Annotation\Header"];
                $headerContainer->mimeType = $headerAnnotations[0]->contentType;
            }

            return $headerContainer;
        };

        // @Filter
        $annotationContainer->filter = function () use ($injectedAnnotation) {
            $filterContainer = new Container();
            $invokeInitializeList = [];
            $invokeBeforeList = [];
            $invokeAfterList = [];
            if (array_key_exists("WebStream\Annotation\Filter", $injectedAnnotation)) {
                $filterAnnotations = $injectedAnnotation["WebStream\Annotation\Filter"];
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

            $filterContainer->initialize = $invokeInitializeList;
            $filterContainer->before = $invokeBeforeList;
            $filterContainer->after = $invokeAfterList;

            return $filterContainer;
        };

        // @Template
        $annotationContainer->template = function () use ($injectedAnnotation, $container) {
            $templateContainer = new Container(false);

            $viewParams = [];
            $baseTemplate = null;
            if (array_key_exists("WebStream\Annotation\Template", $injectedAnnotation)) {
                $templateAnnotations = $injectedAnnotation["WebStream\Annotation\Template"];
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

                $viewParams["model"] = $container->coreDelegator->getService() ?: $container->coreDelegator->getModel();
                $viewParams["helper"] = $container->coreDelegator->getHelper();
            }

            $templateContainer->viewParams = $viewParams;
            $templateContainer->baseTemplate = $baseTemplate;

            return $templateContainer;
        };

        // @TemplateCache
        $annotationContainer->templateCache = function () use ($injectedAnnotation) {
            $templateCacheContainer = new Container(false);
            if (array_key_exists("WebStream\Annotation\TemplateCache", $injectedAnnotation)) {
                $templateCacheAnnotations = $injectedAnnotation["WebStream\Annotation\TemplateCache"];
                $templateCacheContainer->expire = $templateCacheAnnotations[0]->expire;
            }

            return $templateCacheContainer;
        };

        // @ExceptionHandler
        $annotationContainer->exceptionHandler = function () use ($injectedAnnotation) {
            return $injectedAnnotation["WebStream\Annotation\ExceptionHandler"];
        };

        return $annotationContainer;
    }

    /**
     * Modelのアノテーション情報をロードする
     * @param CoreInterface インスタンス
     * @return Container コンテナ
     */
    private function readModel(CoreModel $instance)
    {
        $container = $this->container;
        $reader = new AnnotationReader($instance, $container);
        $reader->read();
        $injectedAnnotation = $reader->getInjectedAnnotationInfo();

        $annotationContainer = new Container();

        // @Database
        $annotationContainer->database = function () use ($injectedAnnotation) {
            return $injectedAnnotation["WebStream\Annotation\Database"];
        };

        // @Query
        $annotationContainer->query = function () use ($injectedAnnotation) {
            return $injectedAnnotation["WebStream\Annotation\Query"];
        };

        return $annotationContainer;
    }
}
