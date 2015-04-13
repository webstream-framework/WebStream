<?php
namespace WebStream\Validate\Rule;

/**
 * Min
 * @author Ryuichi TANAKA.
 * @since 2015/03/30
 * @version 0.4
 */
class Min implements IValidate
{
    /**
     * {@inheritdoc}
     */
    public function isValid($value, $rule)
    {
        $isValid = false;
        if (preg_match('/^min\[([-]?\d{1,}\.?\d{0,}?)\]$/', $rule, $matches)) {
            $isValid = $value === null || doubleval($value) >= doubleval($matches[1]);
        }

        return $isValid;
    }
}
