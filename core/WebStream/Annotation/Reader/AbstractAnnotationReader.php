<?php
namespace WebStream\Annotation\Reader;

use WebStream\Annotation\Container\AnnotationContainer;

/**
 * AbstractAnnotationReader
 * @author Ryuichi TANAKA.
 * @since 2014/05/10
 * @version 0.4
 */
abstract class AbstractAnnotationReader
{
    /** アノテーションリーダオブジェクト */
    protected $reader;

    /**
     * @var object インスタンス
     */
    protected $instance;

    /**
     * @var object アノテーション属性情報
     */
    protected $annotationAttributes;

    /**
     * Constructor
     * @param AnnotationReader アノテーションリーダオブジェクト
     */
    public function __construct(AnnotationReader $reader)
    {
        $this->reader = $reader;
        $this->annotationAttributes = new AnnotationContainer();
        $this->onRead();
    }

    /**
     * onRead event
     */
    abstract public function onRead();

    /**
     * アノテーション処理を実行
     * @param object インスタンス
     */
    abstract public function execute();

    /**
     * アノテーション処理を実行
     * @param object インスタンス
     */
    public function inject($instance)
    {
        $this->instance = $instance;
    }

    /**
     * インスタンスを返却する
     * @return object インスタンス
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * アノテーション属性情報を返却する
     * @return array<string> アノテーション属性情報
     */
    public function getAnnotationAttributes()
    {
        return $this->annotationAttributes;
    }
}
