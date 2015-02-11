<?php
namespace WebStream\Annotation;

use WebStream\Core\CoreInterface;
use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IRead;
use WebStream\Annotation\Base\IMethods;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Module\Logger;
use WebStream\Module\Container;

/**
 * Filter
 * @author Ryuichi TANAKA.
 * @since 2013/10/20
 * @version 0.4
 *
 * @Annotation
 * @Target("METHOD")
 */
class Filter extends Annotation implements IMethods, IRead
{
    /**
     * @var AnnotationContainer アノテーションコンテナ
     */
    private $annotaion;

    /**
     * @var AnnotationContainer 注入結果
     */
    private $injectedContainer;

    /**
     * {@inheritdoc}
     */
    public function onInject(AnnotationContainer $annotation)
    {
        $this->annotation = $annotation;
        $this->injectedContainer = new AnnotationContainer();
        Logger::debug("@Filter injected.");
    }

    /**
     * {@inheritdoc}
     */
    public function onInjected()
    {
        return $this->injectedContainer;
    }

    /**
     * {@inheritdoc}
     */
    public function onMethodInject(CoreInterface &$instance, Container $container, \ReflectionMethod $method)
    {
        $type = $this->annotation->type;
        $only = $this->annotation->only;
        $except = $this->annotation->except;

        $exceptMethods = [];

        // アクションメソッドの@Filter(type="skip")をチェックする
        // 1メソッドに対して複数の@Filterが指定されてもエラーにはしない
        if ($type === "skip") {
            $exceptMethods = $except;
            if (!is_array($except)) {
                $exceptMethods = [$except];
            }
        }

        if (in_array($type, ["before", "after"])) {
            // skip filterが有効なら適用しない
            // クラスに関係なくメソッド名が一致した場合すべて適用しない
            if (in_array($method->getName(), $exceptMethods)) {
                return;
            }
            // only
            if ($only !== null) {
                $onlyList = $only;
                if (!is_array($onlyList)) {
                    $onlyList = [$onlyList];
                }
                // アクションメソッド名がonlyListに含まれていれば実行対象とする
                if (!in_array($action, $onlyList)) {
                    return;
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
                    return;
                }
            }
        }

        // 実行時に動的にインスタンスを渡すようにしないと、各メソッドでの実行結果が反映されないため
        // この時点でのクロージャへのインスタンス設定はせず、リストとして保持するに留める
        $this->injectedContainer->{$type} = $method;
    }
}
