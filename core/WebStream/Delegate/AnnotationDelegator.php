<?php
namespace WebStream\Delegate;

use WebStream\Core\CoreInterface;
use WebStream\Core\CoreController;
use WebStream\Core\CoreModel;
use WebStream\Module\Logger;
use WebStream\Module\Container;
use WebStream\Annotation\Reader\AnnotationReader;
use WebStream\Annotation\Container\AnnotationListContainer;
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
                $headerContainer->mimeType = $headerAnnotations[0]->contentType ?: $headerContainer->mimeType;
            }

            return $headerContainer;
        };

        // @Filter
        $annotationContainer->filter = function () use ($injectedAnnotation) {
            $filterListContainer = new Container();
            $filterListContainer->initialize = new AnnotationListContainer();
            $filterListContainer->before     = new AnnotationListContainer();
            $filterListContainer->after      = new AnnotationListContainer();

            if (array_key_exists("WebStream\Annotation\Filter", $injectedAnnotation)) {
                $filterAnnotations = $injectedAnnotation["WebStream\Annotation\Filter"];
                $exceptMethods = [];

                // アクションメソッドの@Filter(type="skip")をチェックする
                // 1メソッドに対して複数の@Filterが指定されてもエラーにはしない
                foreach ($filterAnnotations as $filterAnnotation) {
                    if ($filterAnnotation->classpath . "#" . $filterAnnotation->action === $filterAnnotation->method->class . "#" . $filterAnnotation->method->name) {
                        if ($filterAnnotation->annotation->type === 'skip') {
                            $exceptMethods = $filterAnnotation->annotation->except;
                            if (!is_array($exceptMethods)) {
                                $exceptMethods = [$exceptMethods];
                            }
                        }
                    }
                }

                $isInitialized = false;
                foreach ($filterAnnotations as $filterAnnotation) {
                    $type = $filterAnnotation->annotation->type;
                    $only = $filterAnnotation->annotation->only;
                    $except = $filterAnnotation->annotation->except;
                    $method = $filterAnnotation->method;
                    $action = $filterAnnotation->action;

                    // initializeはCoreControllerでのみ使用可能なため複数回指定されたら例外
                    if ($type === "initialize") {
                        if ($isInitialized) {
                            throw new AnnotationException("Can not multiple define @Filter(type=\"initialize\") at method.");
                        }
                        $isInitialized = true;
                    } elseif (in_array($type, ["before", "after"])) {
                        // skip filterが有効なら適用しない
                        // クラスに関係なくメソッド名が一致した場合すべて適用しない
                        if (in_array($method->name, $exceptMethods)) {
                            continue;
                        }
                        // only
                        if ($only !== null) {
                            $onlyList = $only;
                            if (!is_array($onlyList)) {
                                $onlyList = [$onlyList];
                            }
                            // アクションメソッド名がonlyListに含まれていれば実行対象とする
                            if (!in_array($action, $onlyList)) {
                                continue;
                            }
                        }
                        // exceptは親クラス以上すべてのメソッドに対して適用するのでメソッド名のみ取得
                        if ($except !== null) {
                            $exceptList = $except;
                            if (!is_array($exceptList)) {
                                $exceptList = [$exceptList];
                            }
                            // アクションメソッド名がexceptListに含まれていれば実行対象としない
                            if (in_array($action, $exceptList)) {
                                continue;
                            }
                        }
                    } else {
                        continue;
                    }
                    // 実行時に動的にインスタンスを渡すようにしないと、各メソッドでの実行結果が反映されないため
                    // この時点でのクロージャへのインスタンス設定はせず、リストとして保持するに留める
                    $filterListContainer->{$type}->push($method);
                }
            }

            return $filterListContainer;
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

                    $baseTemplate = $templateAnnotation->baseCandidate;

                    if ($templateAnnotation->base !== null) {
                        if ($baseTemplate !== null) {
                            // ベーステンプレートが複数指定された場合、エラーとする
                            $errorMsg = "Invalid argument of @Template('" . $template . "') attribute 'type'.";
                            $errorMsg.= "The type attribute 'base' must be a only definition.";
                            throw new AnnotationException($errorMsg);
                        }
                        // if ($templateAnnotation->parts === null) {
                        //     // @Templateが複数定義されていて、2つ目以降にparts属性の指定がない場合、エラーとする
                        //     $errorMsg = "Invalid argument of @Template('" . $template . "') attribute 'type'.";
                        //     $errorMsg.= "The type attribute 'parts' must be a only definition.";
                        //     throw new AnnotationException($errorMsg);
                        // }

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
