<?php
namespace WebStream\Annotation;

use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IProperty;
use WebStream\Core\CoreInterface;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Module\Container;
use WebStream\Module\Logger;
use WebStream\Exception\Extend\AnnotationException;

/**
 * Autowired
 * @author Ryuichi TANAKA.
 * @since 2013/09/17
 * @version 0.4
 *
 * @Annotation
 * @Target("PROPERTY")
 */
class Autowired extends Annotation implements IProperty
{
    /**
     * @var AnnotationContainer アノテーションコンテナ
     */
    private $annotaion;

    /**
     * {@inheritdoc}
     */
    public function onInject(AnnotationContainer $annotation)
    {
        $this->annotation = $annotation;
        Logger::debug("@Autowired injected.");
    }

    /**
     * {@inheritdoc}
     */
    public function onPropertyInject(CoreInterface &$instance, Container $container, \ReflectionProperty $property)
    {
        if ($property->isPrivate() || $property->isProtected()) {
            $property->setAccessible(true);
        }

        $value = $this->annotation->value;
        $type = $this->annotation->type;

        if ($type !== null) {
            // value属性の指定ありの場合、型と一致すれば値を設定する
            if ($value !== null) {
                if (settype($value, $type)) {
                    // 初期値が設定してある場合、Autowiredしない。
                    // メンバ変数に初期値が設定してある場合、または、コンストラクタで
                    // メンバ変数に値を設定した場合(タイミング的にAutowired後に値を上書きしたとみなすため)
                    if ($property->getValue($instance) === null) {
                        $property->setValue($instance, $value);
                    }
                } else {
                    // プリミティブ型の場合、警告を出すだけに留める
                    Logger::warn("Failed to cast '$value' to '$type'.");
                }
            } else { // value属性の指定がない場合、参照型であればインスタンスを代入する
                if (class_exists($type)) {
                    $value = new $type();
                    if ($property->getValue($instance) === null) {
                        $property->setValue($instance, $value);
                    }
                } else {
                    // クラス参照型の場合、存在しないクラスアクセスなので例外を出す
                    throw new AnnotationException("Failed set '$type' instance because can't find class of $type.");
                }
            }
        // type属性なしかつvalue属性ありの場合は指定された値をそのまま設定
        } elseif ($value !== null) {
            if ($property->getValue($instance) === null) {
                $property->setValue($instance, $value);
            }
        } else {
            // 不明な属性が指定された場合、警告を出す
            $key = $property->class . "." . $property->name;
            Logger::warn("An unknown attribute is specified in $key.");
        }
    }
}
