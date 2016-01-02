<?php
namespace WebStream\Delegate;

use WebStream\Core\CoreController;
use WebStream\Module\Container;
use WebStream\Module\Utility\CommonUtils;
use WebStream\Annotation\Container\AnnotationContainer;
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
    use CommonUtils;

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
            "query" => "WebStream\Annotation\Query",
            "alias" => "WebStream\Annotation\Alias"
        ];
    }

    /**
     * CustomAnnotation結果を返却する
     * @param  string アノテーションID
     * @return Callable CustomAnnotation結果
     */
    public function createAnnotationCallable($annotationId)
    {
        $classpath = array_key_exists($annotationId, $this->injectedAnnotationKeys) ?
            $this->injectedAnnotationKeys[$annotationId] : null;
        $annotations = array_key_exists($classpath, $this->injectedAnnotation) ?
            $this->injectedAnnotation[$classpath] : null;
        $annotationCallable = function() {};

        switch ($classpath) {
            case "WebStream\Annotation\Header":
                $annotationCallable = $this->createHeader($annotations);
                break;
            case "WebStream\Annotation\Filter":
                $annotationCallable = $this->createFilter($annotations);
                break;
            case "WebStream\Annotation\Template":
                $annotationCallable = $this->createTemplate($annotations);
                break;
            case "WebStream\Annotation\ExceptionHandler":
                $annotationCallable = $this->createExceptionHandler($annotations);
                break;
            case "WebStream\Annotation\Database":
                $annotationCallable = $this->createDatabase($annotations);
                break;
            case "WebStream\Annotation\Query":
                $annotationCallable = $this->createQuery($annotations);
                break;
            case "WebStream\Annotation\Alias":
                $annotationCallable = $this->createAlias($annotations);
                break;
        }

        return $annotationCallable;
    }

    /**
     * CustomAnnotation結果を返却する
     * @return Callable CustomAnnotation結果
     */
    public function createCustomAnnotationCallable()
    {
        $injectedAnnotation = $this->injectedAnnotation;
        foreach ($this->injectedAnnotationKeys as $annotationKey) {
            unset($injectedAnnotation[$annotationKey]);
        }

        return function () use ($injectedAnnotation) {
            return $injectedAnnotation;
        };
    }

    /**
     * Header結果を返却する
     * @param  array<AnnotationContainer> アノテーションコンテナリスト
     * @return Callable Header結果
     */
    private function createHeader($containerList)
    {
        return function () use ($containerList) {
            $headerContainer = new Container();
            $headerContainer->mimeType = "html";
            if ($containerList !== null) {
                $headerContainer->mimeType = $containerList[0]->contentType ?: $headerContainer->mimeType;
            }

            return $headerContainer;
        };
    }

    /**
     * Filter結果を返却する
     * @param  array<AnnotationContainer> アノテーションコンテナリスト
     * @return Callable Filter結果
     */
    private function createFilter($containerList)
    {
        return function () use ($containerList) {
            $filterListContainer = new Container();
            $filterListContainer->initialize = new AnnotationListContainer();
            $filterListContainer->before     = new AnnotationListContainer();
            $filterListContainer->after      = new AnnotationListContainer();

            if ($containerList !== null) {
                $exceptMethods = [];

                // アクションメソッドの@Filter(type="skip")をチェックする
                // 1メソッドに対して複数の@Filterが指定されてもエラーにはしない
                foreach ($containerList as $filterAnnotation) {
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
                foreach ($containerList as $filterAnnotation) {
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
     * @param  array<AnnotationContainer> アノテーションコンテナリスト
     * @return Callable Template結果
     */
    private function createTemplate($containerList)
    {
        return function () use ($containerList) {
            $templateContainer = new Container(false);
            $templateContainer->engine = $containerList !== null ? $containerList[0]->engine : null;
            $templateContainer->cacheTime = $containerList !== null ? $containerList[0]->cacheTime : null;

            return $templateContainer;
        };
    }

    /**
     * ExceptionHandler結果を返却する
     * @param  array<AnnotationContainer> アノテーションコンテナリスト
     * @return Callable ExceptionHandler結果
     */
    private function createExceptionHandler($containerList)
    {
        return function () use ($containerList) {
            return $containerList === null ? [] : $containerList;
        };
    }

    /**
     * Database結果を返却する
     * @param  array<AnnotationContainer> アノテーションコンテナリスト
     * @return Callable Database結果
     */
    private function createDatabase($containerList)
    {
        return function () use ($containerList) {
            return $containerList;
        };
    }

    /**
     * Query結果を返却する
     * @param  array<AnnotationContainer> アノテーションコンテナリスト
     * @return Callable Query結果
     */
    private function createQuery($containerList)
    {
        return function () use ($containerList) {
            return $containerList;
        };
    }

    /**
     * Alias結果を返却する
     * @param  array<AnnotationContainer> アノテーションコンテナリスト
     * @return Callable Alias結果
     */
    private function createAlias($containerList)
    {
        return function () use ($containerList) {
            return $containerList === null ? [] : $containerList;;
        };
    }
}
