<?php
namespace WebStream\Validate\Rule;

/**
 * Max
 * @author Ryuichi TANAKA.
 * @since 2015/03/30
 * @version 0.4
 */
class Max implements IValidate
{
    /**
     * {@inheritdoc}
     */
    public function isValid($value, $rule)
    {
        $isValid = false;
        if (preg_match('/^max\[([-]?\d+\.?\d+?)\]$/', $rule, $matches)) {
            $isValid = doubleval($value) <= doubleval($matches[1]);
        }

        return $isValid;
    }
}
