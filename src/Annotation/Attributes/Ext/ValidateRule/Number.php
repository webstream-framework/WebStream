<?php
namespace WebStream\Annotation\Attributes\Ext\ValidateRule;

/**
 * Number
 * @author Ryuichi TANAKA.
 * @since 2015/03/30
 * @version 0.4
 */
class Number implements IValidate
{
    /**
     * {@inheritdoc}
     */
    public function isValid($value, $rule)
    {
        return $value === null || is_numeric($value);
    }
}
