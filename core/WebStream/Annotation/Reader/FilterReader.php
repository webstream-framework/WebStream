<?php
namespace WebStream\Annotation\Reader;

use WebStream\Core\CoreInterface;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Annotation\Container\AnnotationListContainer;
use WebStream\Exception\Extend\AnnotationException;
use Doctrine\Common\Annotations\AnnotationException as DoctrineAnnotationException;

/**
 * FilterReader
 * @author Ryuichi TANAKA.
 * @since 2013/09/18
 * @version 0.4
 */
class FilterReader extends AbstractAnnotationReader
{
    /** アノテーションコンテナ */
    private $annotation;

    /** インスタンス */
    private $instance;

    /** フィルタコンテナ */
    private $filterContainer;

    /**
     * {@inheritdoc}
     */
    public function onRead()
    {
        $this->annotation = $this->reader->getAnnotation("WebStream\Annotation\Filter");
        $this->filterContainer = new AnnotationContainer();
        $this->filterContainer->initialize = new AnnotationListContainer();
        $this->filterContainer->before = new AnnotationListContainer();
        $this->filterContainer->after = new AnnotationListContainer();
    }

    /**
     * インスタンスを設定する
     * @param CoreInterface インスタンス
     */
    public function inject(CoreInterface $instance)
    {
        $this->instance = $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->annotation === null) {
            return;
        }

        try {
            $refClass = $this->reader->getReflectionClass();
            $action = $this->reader->getContainer()->router->action();
            $isInitialized = false;
            $exceptMethods = [];

            // アクションメソッドの@Filter(type="skip")をチェックする
            // 1メソッドに大して複数の@Filterが指定されてもエラーにはしない
            $actionAnnotationKey = $refClass->getName() . "#" . $action;
            if (array_key_exists($actionAnnotationKey, $this->annotation)) {
                $actionContainerList = $this->annotation[$actionAnnotationKey];
                foreach ($actionContainerList as $actionContainer) {
                    if ($actionContainer->type === "skip") {
                        $exceptMethods = $actionContainer->except;
                        if (!is_array($exceptMethods)) {
                            $exceptMethods = [$exceptMethods];
                        }
                    }
                }
            }

            while ($refClass !== false) {
                $refMethods = $refClass->getMethods();
                foreach ($refMethods as $refMethod) {
                    // アクションメソッド自体もフィルタの対象(Railsの仕様に合わせる)
                    // 重複して実行しないようにする
                    if ($refClass->getName() !== $refMethod->class) {
                        continue;
                    }
                    $annotationMapKey = $refClass->getName() . "#" . $refMethod->getName();
                    if (array_key_exists($annotationMapKey, $this->annotation)) {
                        $containerList = $this->annotation[$annotationMapKey];

                        foreach ($containerList as $container) {
                            // initializeはCoreControllerでのみ使用可能なため複数回指定されたら例外
                            if ($container->type === "initialize") {
                                if ($isInitialized) {
                                    throw new AnnotationException("Can not multiple define @Filter(type=\"initialize\") at method.");
                                }
                                $isInitialized = true;
                            } elseif (in_array($container->type, ["before", "after"])) {
                                // skip filterが有効なら適用しない
                                // クラスに関係なくメソッド名が一致した場合すべて適用しない
                                if (in_array($refMethod->getName(), $exceptMethods)) {
                                    continue;
                                }
                                // only
                                if ($container->only !== null) {
                                    $onlyList = $container->only;
                                    if (!is_array($onlyList)) {
                                        $onlyList = [$onlyList];
                                    }
                                    // アクションメソッド名がonlyListに含まれていれば実行対象とする
                                    if (!in_array($action, $onlyList)) {
                                        continue;
                                    }
                                }
                                // exceptは親クラス以上すべてのメソッドに対して適用するのでメソッド名のみ取得
                                if ($container->except !== null) {
                                    $exceptList = $container->except;
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
                            $this->filterContainer->{$container->type}->push($refMethod);
                        }
                    }
                }

                $refClass = $refClass->getParentClass();
            }
        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e->getMessage());
        }
    }

    /**
     * initialize filterを実行する
     */
    public function initialize()
    {
        foreach ($this->filterContainer->initialize as $refMethod) {
            $refMethod->invoke($this->instance);
        }
    }

    /**
     * before filterを実行する
     */
    public function before()
    {
        foreach ($this->filterContainer->before as $refMethod) {
            $refMethod->invoke($this->instance);
        }
    }

    /**
     * after filterを実行する
     */
    public function after()
    {
        foreach ($this->filterContainer->after as $refMethod) {
            $refMethod->invoke($this->instance);
        }
    }

    /**
     * インスタンスを返却する
     * return CoreInterface インスタンス
     */
    public function getInstance()
    {
        return $this->instance;
    }
}
