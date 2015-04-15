<?php
namespace WebStream\Validate\Rule;

/**
 * Range
 * @author Ryuichi TANAKA.
 * @since 2015/03/30
 * @version 0.4
 */
class Range implements IValidate
{
    /**
     * {@inheritdoc}
     */
    public function isValid($value, $rule)
    {
        $isValid = false;
        if (preg_match('/^range\[([-]?\d{1,}\.?\d{0,}?)\.\.([-]?\d{1,}\.?\d{0,}?)\]$/', $rule, $matches)) {
            $value = doubleval($value);
            $isValid = $value === null || ($value >= doubleval($matches[1]) && $value <= doubleval($matches[2]));
        }

        return $isValid;
    }
}
