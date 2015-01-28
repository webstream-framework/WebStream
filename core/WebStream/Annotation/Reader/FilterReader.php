<?php
namespace WebStream\Annotation\Reader;

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
class FilterReader extends AbstractAnnotationReader implements AnnotationReadInterface
{
    /**
     * @var AnnotationContainer アノテーションコンテナ
     */
    private $annotation;

    /**
     * {@inheritdoc}
     */
    public function onRead()
    {
        $this->annotation = $this->reader->getAnnotation("WebStream\Annotation\Filter");
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        $annotationContainer = new AnnotationContainer();
        $annotationContainer->initialize = new AnnotationListContainer();
        $annotationContainer->before = new AnnotationListContainer();
        $annotationContainer->after = new AnnotationListContainer();

        if ($this->annotation === null) {
            return $annotationContainer;
        }

        try {
            $container = $this->reader->getContainer();
            $isInitialized = false;
            $exceptMethods = [];

            // アクションメソッドの@Filter(type="skip")をチェックする
            // 1メソッドに対して複数の@Filterが指定されてもエラーにはしない
            $action = $container->action;
            $actionAnnotationKey = $container->classpath . "#" . $action;
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

            $refClass = $this->reader->getReflectionClass();
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
                            $annotationContainer->{$container->type}->push($refMethod);
                        }
                    }
                }

                $refClass = $refClass->getParentClass();
            }
        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e);
        }

        return $annotationContainer;
    }
}
