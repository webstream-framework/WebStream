<?php
namespace WebStream\Annotation\Attributes\Ext\ValidateRule;

/**
 * Regexp
 * @author Ryuichi TANAKA.
 * @since 2015/04/01
 * @version 0.4
 */
class Regexp implements IValidate
{
    /**
     * {@inheritdoc}
     */
    public function isValid($value, $rule)
    {
        $isValid = false;
        if (preg_match('/^regexp\[(\/.*?\/[a-z]*)\]$/', $rule, $matches)) {
            $isValid = $value === null || preg_match($matches[1], $value);
        }

        return $isValid;
    }
}
