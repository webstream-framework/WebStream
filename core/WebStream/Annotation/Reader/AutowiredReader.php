<?php
namespace WebStream\Annotation\Reader;

use WebStream\Core\CoreInterface;
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

        if ($this->instance === null) {
            throw new AnnotationException("Can't find autowired instance.");
        }

        $refClass = $this->reader->getReflectionClass();

        try {
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
                            if (!settype($value, $type)) {
                                Logger::warn("Failed to cast '$value' to '$type'.");
                            }
                        }
                        if ($value !== null) {
                            if ($property->isPrivate() || $property->isProtected()) {
                                $property->setAccessible(true);
                            }
                            // 初期値が設定してある場合、Autowiredしない。
                            // メンバ変数に初期値が設定してある場合、または、コンストラクタで
                            // メンバ変数に値を設定した場合(タイミング的にAutowired後に値を上書きしたとみなすため)
                            if ($property->getValue($this->instance) === null) {
                                $property->setValue($this->instance, $value);
                            }
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
     * インスタンスを返却する
     * @return CoreInterface インスタンス
     */
    public function getInstance()
    {
        return $this->instance;
    }
}
