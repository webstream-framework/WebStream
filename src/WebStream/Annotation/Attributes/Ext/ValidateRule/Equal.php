<?php
namespace WebStream\Validate\Rule;

/**
 * Equal
 * @author Ryuichi TANAKA.
 * @since 2015/03/30
 * @version 0.4
 */
class Equal implements IValidate
{
    /**
     * {@inheritdoc}
     */
    public function isValid($value, $rule)
    {
        $isValid = false;
        if (preg_match('/^equal\[(.+)\]$/', $rule, $matches)) {
            $isValid = $value === null || $value === $matches[1];
        }

        return $isValid;
    }
}
