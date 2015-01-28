<?php
namespace WebStream\Annotation\Reader;

/**
 * AnnotationInjectInterface
 * @author Ryuichi TANAKA.
 * @since 2015/01/27
 * @version 0.4.0
 */
interface AnnotationInjectInterface
{
    /**
     * 依存性注入インスタンスを設定する
     * @param object インスタンス
     */
    public function inject(&$instance);
}
