<?php
namespace WebStream\Annotation\Attributes\Ext\ValidateRule;

/**
 * MaxLength
 * @author Ryuichi TANAKA.
 * @since 2015/03/30
 * @version 0.4
 */
class MaxLength implements IValidate
{
    /**
     * {@inheritdoc}
     */
    public function isValid($value, $rule)
    {
        $isValid = false;
        if (preg_match('/^max_length\[(0|[1-9]\d*)\]$/', $rule, $matches)) {
            $isValid = $value === null || intval($matches[1]) >= mb_strlen($value, "UTF-8");
        }

        return $isValid;
    }
}
