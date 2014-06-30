<?php
namespace WebStream\Annotation\Reader;

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
     * Constructor
     * @param AnnotationReader アノテーションリーダオブジェクト
     */
    public function __construct(AnnotationReader $reader)
    {
        $this->reader = $reader;
        $this->onRead();
    }

    /**
     * onRead event
     */
    abstract public function onRead();

    /**
     * アノテーション処理を実行
     */
    abstract public function execute();
}
