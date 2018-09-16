<?php
namespace WebStream\Annotation\Reader\Extend;

use WebStream\Container\Container;
use WebStream\Annotation\Container\AnnotationListContainer;

/**
 * FilterExtendReader
 * @author Ryuichi Tanaka
 * @since 2017/01/08
 * @version 0.7
 */
class FilterExtendReader extends ExtendReader
{
    /**
     * @var array<Container> アノテーション情報リスト
     */
    private $annotationInfo;

    /**
     * {@inheritdoc}
     */
    public function getAnnotationInfo()
    {
        return $this->annotationInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function read(array $annotationInfoList)
    {
        $filterListContainer = new Container();
        $filterListContainer->initialize = new AnnotationListContainer();
        $filterListContainer->before = new AnnotationListContainer();
        $filterListContainer->after = new AnnotationListContainer();

        $exceptMethods = [];
        // アクションメソッドの@Filter(type="skip")をチェックする
        // 1メソッドに対して複数の@Filterが指定されてもエラーにはしない
        foreach ($annotationInfoList as $annotationInfo) {
            if ($annotationInfo['classpath'] . "#" . $annotationInfo['action'] === $annotationInfo['refMethod']->class . "#" . $annotationInfo['refMethod']->name) {
                if ($annotationInfo['annotation']->type === 'skip') {
                    $exceptMethods = $annotationInfo['annotation']->except;
                    if (!is_array($exceptMethods)) {
                        $exceptMethods = [$exceptMethods];
                    }
                }
            }
        }

        $isInitialized = false;
        foreach ($annotationInfoList as $annotationInfo) {
            $type = $annotationInfo['annotation']->type;
            $only = $annotationInfo['annotation']->only;
            $except = $annotationInfo['annotation']->except;
            $refMethod = $annotationInfo['refMethod'];
            $action = $annotationInfo['action'];
            // initializeはCoreControllerでのみ使用可能なため複数回指定されたら例外
            if ($type === "initialize") {
                if ($isInitialized) {
                    throw new AnnotationException("Can not multiple define @Filter(type=\"initialize\") at method.");
                }
                $isInitialized = true;
            } elseif (array_key_exists($type, array_flip(["before", "after"]))) {
                // skip filterが有効なら適用しない
                // クラスに関係なくメソッド名が一致した場合すべて適用しない
                if (array_key_exists($refMethod->name, array_flip($exceptMethods))) {
                    continue;
                }
                // only
                if ($only !== null) {
                    $onlyList = $only;
                    if (!is_array($onlyList)) {
                        $onlyList = [$onlyList];
                    }
                    // アクションメソッド名がonlyListに含まれていれば実行対象とする
                    if (!array_key_exists($action, array_flip($onlyList))) {
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
                    if (array_key_exists($action, array_flip($exceptList))) {
                        continue;
                    }
                }
            } else {
                continue;
            }
            // 実行時に動的にインスタンスを渡すようにしないと、各メソッドでの実行結果が反映されないため
            // この時点でのクロージャへのインスタンス設定はせず、リストとして保持するに留める
            $filterListContainer->{$type}->push($refMethod);
        }

        $this->annotationInfo = $filterListContainer;
    }
}
