<?php
namespace WebStream\Annotation\Reader;

use WebStream\Module\Logger;
use WebStream\Exception\Extend\AnnotationException;
use Doctrine\Common\Annotations\AnnotationException as DoctrineAnnotationException;

/**
 * AutowiredReader
 * @author Ryuichi TANAKA.
 * @since 2013/09/18
 * @version 0.4.1
 */
class AutowiredReader extends AbstractAnnotationReader
{
    /** アノテーションコンテナ */
    private $annotation;

    /** Autowired実行済みインスタンス */
    private $instance;

    /**
     * {@inheritdoc}
     */
    public function onRead()
    {
        $this->annotation = $this->reader->getAnnotation("WebStream\Annotation\Autowired");
    }

    /**
     * インスタンスを設定する
     * @param object 注入対象インスタンス
     */
    public function inject($instance)
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

        if ($this->instance === null) {
            throw new AnnotationException("Can't find autowired instance.");
        }

        try {
            $refClass = $this->reader->getReflectionClass();
            while ($refClass !== false) {
                $properties = $refClass->getProperties();
                foreach ($properties as $property) {
                    if ($property->isPrivate() || $property->isProtected()) {
                        $property->setAccessible(true);
                    }
                    $key = $refClass->getName() . "." . $property->getName();
                    if (array_key_exists($key, $this->annotation)) {
                        $container = $this->annotation[$key];
                        $value = $container->value;
                        $type = $container->type;
                        if ($type !== null) {
                            if ($property->isPrivate() || $property->isProtected()) {
                                $property->setAccessible(true);
                            }
                            // value属性の指定ありの場合、型と一致すれば値を設定する
                            if ($value !== null) {
                                if (settype($value, $type)) {
                                    // 初期値が設定してある場合、Autowiredしない。
                                    // メンバ変数に初期値が設定してある場合、または、コンストラクタで
                                    // メンバ変数に値を設定した場合(タイミング的にAutowired後に値を上書きしたとみなすため)
                                    if ($property->getValue($this->instance) === null) {
                                        $property->setValue($this->instance, $value);
                                    }
                                } else {
                                    // プリミティブ型の場合、警告を出すだけに留める
                                    Logger::warn("Failed to cast '$value' to '$type'.");
                                }
                            } else { // value属性の指定がない場合、参照型であればインスタンスを代入する
                                if (class_exists($type)) {
                                    $value = new $type();
                                    if ($property->getValue($this->instance) === null) {
                                        $property->setValue($this->instance, $value);
                                    }
                                } else {
                                    // クラス参照型の場合、存在しないクラスアクセスなので例外を出す
                                    throw new AnnotationException("Failed set '$type' instance because can't find class of $type.");
                                }
                            }
                        // type属性なしかつvalue属性ありの場合は指定された値をそのまま設定
                        } elseif ($value !== null) {
                            if ($property->getValue($this->instance) === null) {
                                $property->setValue($this->instance, $value);
                            }
                        } else {
                            // 不明な属性が指定された場合、警告を出す
                            Logger::warn("An unknown attribute is specified in $key.");
                        }
                    }
                }
                $refClass = $refClass->getParentClass();
            }
        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e);
        }
    }

    /**
     * インスタンスを返却する
     * @return CoreInterface インスタンス
     */
    public function getInstance()
    {
        return $this->instance;
    }
}
