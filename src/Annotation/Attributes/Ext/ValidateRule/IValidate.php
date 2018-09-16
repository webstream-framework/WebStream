<?php
namespace WebStream\Annotation\Attributes\Ext\ValidateRule;

/**
 * IValidate
 * @author Ryuichi TANAKA.
 * @since 2015/03/30
 * @version 0.4
 */
interface IValidate
{
    /**
     * 妥当性を検証する
     * @param mixed 検証する値
     * @param string 検証ルール
     */
    public function isValid($value, $rule);
}
