<?php
namespace WebStream\Delegate;

use WebStream\Core\CoreController;
use WebStream\Module\Container;
use WebStream\Module\Utility;
use WebStream\Annotation\Container\AnnotationListContainer;
use WebStream\Exception\Extend\AnnotationException;

/**
 * AnnotationDelegatorFactory
 * @author Ryuichi TANAKA.
 * @since 2015/02/23
 * @version 0.4
 */
class AnnotationDelegatorFactory
{
    use Utility;

    /**
     * @var Container 注入結果のコンテナ
     */
    private $injectedAnnotation;

    /**
     * @var array<string> 注入されたキー/クラスパスのマップ
     */
    private $injectedAnnotationKeys;

    /**
     * @var Container 依存コンテナ
     */
    private $container;

    /**
     * Constructor
     * @param array<string> 注入後の返却情報
     */
    public function __construct(array $injectedAnnotation, Container $container)
    {
        $this->container = $container;
        $this->injectedAnnotation = $injectedAnnotation;
        $this->injectedAnnotationKeys = [
            "header" => "WebStream\Annotation\Header",
            "filter" => "WebStream\Annotation\Filter",
            "template" => "WebStream\Annotation\Template",
            "exceptionHandler" => "WebStream\Annotation\ExceptionHandler",
            "database" => "WebStream\Annotation\Database",
            "query" => "WebStream\Annotation\Query"
        ];
    }

    /**
     * Header結果を返却する
     * @return Callable Header結果
     */
    public function createHeader()
    {
        $headerAnnotations = null;
        if (array_key_exists($this->injectedAnnotationKeys["header"], $this->injectedAnnotation)) {
            $headerAnnotations = $this->injectedAnnotation[$this->injectedAnnotationKeys["header"]];
        }

        return function () use (&$headerAnnotations) {
            $headerContainer = new Container();
            $headerContainer->mimeType = "html";
            if ($headerAnnotations !== null) {
                $headerContainer->mimeType = $headerAnnotations[0]->contentType ?: $headerContainer->mimeType;
            }

            return $headerContainer;
        };
    }

    /**
     * Filter結果を返却する
     * @return Callable Filter結果
     */
    public function createFilter()
    {
        $filterAnnotations = null;
        if (array_key_exists($this->injectedAnnotationKeys["filter"], $this->injectedAnnotation)) {
            $filterAnnotations = $this->injectedAnnotation[$this->injectedAnnotationKeys["filter"]];
        }

        return function () use ($filterAnnotations) {
            $filterListContainer = new Container();
            $filterListContainer->initialize = new AnnotationListContainer();
            $filterListContainer->before     = new AnnotationListContainer();
            $filterListContainer->after      = new AnnotationListContainer();

            if ($filterAnnotations !== null) {
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
                    } elseif ($this->inArray($type, ["before", "after"])) {
                        // skip filterが有効なら適用しない
                        // クラスに関係なくメソッド名が一致した場合すべて適用しない
                        if ($this->inArray($method->name, $exceptMethods)) {
                            continue;
                        }
                        // only
                        if ($only !== null) {
                            $onlyList = $only;
                            if (!is_array($onlyList)) {
                                $onlyList = [$onlyList];
                            }
                            // アクションメソッド名がonlyListに含まれていれば実行対象とする
                            if (!$this->inArray($action, $onlyList)) {
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
                            if ($this->inArray($action, $exceptList)) {
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
    }

    /**
     * Template結果を返却する
     * @return Callable Template結果
     */
    public function createTemplate()
    {
        $templateAnnotations = null;
        if (array_key_exists($this->injectedAnnotationKeys["template"], $this->injectedAnnotation)) {
            $templateAnnotations = $this->injectedAnnotation[$this->injectedAnnotationKeys["template"]];
        }

        return function () use ($templateAnnotations) {
            $templateContainer = new Container(false);
            $templateContainer->engine = $templateAnnotations[0]->engine;
            $templateContainer->cacheTime = $templateAnnotations[0]->cacheTime;

            return $templateContainer;
        };
    }

    /**
     * ExceptionHandler結果を返却する
     * @return Callable ExceptionHandler結果
     */
    public function createExceptionHandler()
    {
        $exceptionHandlerAnnotations = [];
        if (array_key_exists($this->injectedAnnotationKeys["exceptionHandler"], $this->injectedAnnotation)) {
            $exceptionHandlerAnnotations = $this->injectedAnnotation[$this->injectedAnnotationKeys["exceptionHandler"]];
        }

        return function () use ($exceptionHandlerAnnotations) {
            return $exceptionHandlerAnnotations;
        };
    }

    /**
     * Database結果を返却する
     * @return Callable Database結果
     */
    public function createDatabase()
    {
        $databaseAnnotations = null;
        if (array_key_exists($this->injectedAnnotationKeys["database"], $this->injectedAnnotation)) {
            $databaseAnnotations = $this->injectedAnnotation[$this->injectedAnnotationKeys["database"]];
        }

        return function () use ($databaseAnnotations) {
            return $databaseAnnotations;
        };
    }

    /**
     * Query結果を返却する
     * @return Callable Query結果
     */
    public function createQuery()
    {
        $queryAnnotations = null;
        if (array_key_exists($this->injectedAnnotationKeys["query"], $this->injectedAnnotation)) {
            $queryAnnotations = $this->injectedAnnotation[$this->injectedAnnotationKeys["query"]];
        }

        return function () use ($queryAnnotations) {
            return $queryAnnotations;
        };
    }

    /**
     * CustomAnnotation結果を返却する
     * @return Callable CustomAnnotation結果
     */
    public function createCustomAnnotation()
    {
        $injectedAnnotation = $this->injectedAnnotation;
        foreach ($this->injectedAnnotationKeys as $annotationKey) {
            unset($injectedAnnotation[$annotationKey]);
        }

        return function () use ($injectedAnnotation) {
            return $injectedAnnotation;
        };
    }
}
