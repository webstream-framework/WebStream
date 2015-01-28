<?php
namespace WebStream\Annotation\Reader;

/**
 * AnnotationReadInterface
 * @author Ryuichi TANAKA.
 * @since 2015/01/27
 * @version 0.4.0
 */
interface AnnotationReadInterface
{
    /**
     * アノテーション属性情報を読み込み返却する
     * @return AnnotationContainer アノテーション属性情報
     */
    public function read();
}
