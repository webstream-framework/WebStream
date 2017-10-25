<?php
namespace WebStream\Validate\Rule;

/**
 * Required
 * @author Ryuichi TANAKA.
 * @since 2015/03/31
 * @version 0.4
 */
class Required implements IValidate
{
    /**
     * {@inheritdoc}
     */
    public function isValid($value, $rule)
    {
        return $value !== null && $value !== "";
    }
}
