<?php
namespace WebStream\Annotation\Attributes\Ext\ValidateRule;

/**
 * Length
 * @author Ryuichi TANAKA.
 * @since 2015/03/30
 * @version 0.4
 */
class Length implements IValidate
{
    /**
     * {@inheritdoc}
     */
    public function isValid($value, $rule)
    {
        $isValid = false;
        if (preg_match('/^length\[(0|[1-9]\d*)\]$/', $rule, $matches)) {
            $isValid = $value === null || mb_strlen($value, "UTF-8") === intval($matches[1]);
        }

        return $isValid;
    }
}
