<?php
namespace WebStream\Annotation\Base;

/**
 * IMethod
 * @author Ryuichi TANAKA.
 * @since 2015/02/10
 * @version 0.4
 */
interface IRead
{
    /**
     * 注入結果を返却する
     * @return WebStream\Annotation\Container\AnnotationContainer 注入結果
     */
    public function onInjected();
}
